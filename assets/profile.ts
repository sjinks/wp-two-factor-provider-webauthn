/* eslint-disable camelcase */
// eslint-disable-next-line import/no-unresolved -- we use jQuery from WordPress
import jQuery from 'jquery';
import {
	PublicKeyCredentialCreationOptionsPlain,
	preparePublicKeyCreationOptions,
	preparePublicKeyCredential,
	decodeDOMException,
} from './common';
import {
	L_UNKNOWN_ERROR,
	L_FETCHING_REG_INFO,
	L_GENERATING_CREDENTIALS,
	L_REGISTERING_CREDENTIALS,
	L_FAILED_TO_CREATE_CREDENTIALS,
	L_KEY_REGISTERED,
	L_SENDING_REQUEST,
	L_KEY_REVOKED,
	L_KEY_RENAMED,
} from './lang';

declare let ajaxurl: string;

declare let tfa_webauthn: {
	nonce: string;
};

interface PreregisterResponse {
	data: {
		nonce: string;
		options: PublicKeyCredentialCreationOptionsPlain;
	};
}

interface RegisterResponse {
	data: {
		nonce: string;
		row: string;
	};
}

interface RenameResponse {
	success: true;
	data: {
		name: string;
	};
}

function ajaxRequest<T>(data: string | JQuery.PlainObject): Promise<T> {
	return new Promise<T>((resolve, reject) => {
		jQuery
			.ajax({
				method: 'POST',
				url: ajaxurl,
				data,
			})
			.done((response: T) => {
				resolve(response);
			})
			.fail((response) => {
				let message;
				if (response.responseJSON) {
					message = response.responseJSON.data || L_UNKNOWN_ERROR;
				} else {
					message = response.statusText;
				}

				reject(new Error(message));
			});
	});
}

jQuery(($) => {
	const parent = $('#webauthn-security-keys-section');

	function updateStatus(status: string): void {
		if (!status) {
			parent.find('.security-key-status').text('');
		} else {
			parent
				.find('.security-key-status')
				.html('<div class="notice notice-info inline"><p>' + status + '</p></div>');
		}
	}

	function errorHandler(e: Error): void {
		const message = e instanceof DOMException ? decodeDOMException(e, false) : e.message;
		const table = parent.find('.registered-keys');
		table.siblings('.notice').remove();
		table.before('<div class="notice notice-error inline" role="alert"><p>' + message + '</p></div>');
	}

	function startRegistration() {
		parent.find('.registered-keys').prev('.notice').remove();
		updateStatus(L_FETCHING_REG_INFO);

		ajaxRequest<PreregisterResponse>({
			action: 'webauthn_preregister',
			_ajax_nonce: tfa_webauthn.nonce,
		})
			.then((response) => {
				updateStatus(L_GENERATING_CREDENTIALS);
				tfa_webauthn.nonce = response.data.nonce;
				const publicKey = preparePublicKeyCreationOptions(response.data.options);
				return navigator.credentials.create({
					publicKey,
				}) as Promise<PublicKeyCredential | null>;
			})
			.then((c: PublicKeyCredential | null) => {
				if (c) {
					updateStatus(L_REGISTERING_CREDENTIALS);
					const name = $('#webauthn-key-name').val();
					return ajaxRequest<RegisterResponse>({
						action: 'webauthn_register',
						_ajax_nonce: tfa_webauthn.nonce,
						credential: JSON.stringify(preparePublicKeyCredential(c)),
						name,
					});
				}

				throw new Error(L_FAILED_TO_CREATE_CREDENTIALS);
			})
			.then((response) => {
				tfa_webauthn.nonce = response.data.nonce;
				const table = parent.find('.registered-keys');
				table.find('tbody > tr:last-child').after(response.data.row);
				table.find('tbody > tr.no-items').remove();
				table.before(
					'<div class="notice notice-success inline" role="alert"><p>' + L_KEY_REGISTERED + '</p></div>',
				);
			})
			.catch(errorHandler)
			.finally(() => {
				updateStatus('');
				$('#webauthn-key-name').val('');
			});
	}

	parent.find('.add-webauthn-key button').on('click', startRegistration);

	parent.find('.registered-keys').on('click', 'tbody .delete a', (e) => {
		parent.find('.registered-keys').prev('.notice').remove();
		e.preventDefault();
		const a = $(e.target);
		const actions = a.closest('.row-actions');
		if (actions.siblings('.confirm-revoke').length) {
			return;
		}

		const handle: string = a.data('handle');
		const nonce: string = a.data('nonce');
		const table = parent.find('.registered-keys');

		const tpl = $($('#webauthn-revoke-confirm').text());
		actions.after(tpl);
		actions
			.siblings('.confirm-revoke')
			.on('click', '.button-secondary', () => {
				actions.siblings('.confirm-revoke').remove();
			})
			.on('click', '.button-link-delete', () => {
				actions.siblings('.confirm-revoke').hide();
				updateStatus(L_SENDING_REQUEST);
				return ajaxRequest<unknown>({
					action: 'webauthn_delete_key',
					_ajax_nonce: nonce,
					handle,
				})
					.then(() => {
						table.before(
							'<div class="notice notice-success inline" role="alert"><p>' + L_KEY_REVOKED + '</p></div>',
						);
						a.closest('tr').remove();
						if (!table.find('tbody > tr').length) {
							table.find('tbody').append($('#webauthn-no-keys').text());
						}
					})
					.catch(errorHandler)
					.finally(() => {
						updateStatus('');
						actions.siblings('.confirm-revoke').remove();
					});
			});
	});

	parent.find('.registered-keys').on('click', 'tbody .rename a', (e) => {
		parent.find('.registered-keys').prev('.notice').remove();
		e.preventDefault();
		const a = $(e.target);
		const actions = a.closest('.row-actions');
		if (actions.siblings('.rename-key').length) {
			return;
		}

		const handle: string = a.data('handle');
		const nonce: string = a.data('nonce');
		const name = a.closest('td').find('span.key-name').text().trim();
		const table = parent.find('.registered-keys');

		const tpl = $($('#webauthn-rename-key').text());
		actions.after(tpl);
		actions
			.siblings('.rename-key')
			.on('click', '.button-secondary', () => {
				actions.siblings('.rename-key').remove();
			})
			.on('click', '.button-primary', () => {
				const keyname = actions.siblings('.rename-key').find('input[type="text"]').val() as string;
				actions.siblings('.rename-key').hide();
				updateStatus(L_SENDING_REQUEST);
				return ajaxRequest<RenameResponse>({
					action: 'webauthn_rename_key',
					_ajax_nonce: nonce,
					handle,
					name: keyname,
				})
					.then((r) => {
						table.before(
							'<div class="notice notice-success inline" role="alert"><p>' + L_KEY_RENAMED + '</p></div>',
						);

						a.closest('td').find('span.key-name').text(r.data.name);
					})
					.catch(errorHandler)
					.finally(() => {
						updateStatus('');
						actions.siblings('.rename-key').remove();
					});
			})
			.find('input[type="text"]')
			.val(name);
	});
});
