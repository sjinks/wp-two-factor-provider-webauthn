/* eslint-disable camelcase */
import {
	PublicKeyCredentialRequestOptionsPlain,
	preparePublicKeyCredentialRequestOptions,
	preparePublicKeyCredential,
	decodeDOMException,
} from './common';
import { L_WEBAUTHN_NOT_SUPPORTED, L_UNABLE_TO_GET_PK_CREDENTIAL } from './lang';

declare let tfa_webauthn: {
	options: PublicKeyCredentialRequestOptionsPlain;
};

function showError(error: string): void {
	const el = document.getElementById('login_error');
	if (el && el.parentNode) {
		el.parentNode.removeChild(el);
	}

	const form = document.getElementById('loginform');
	if (form) {
		form.insertAdjacentHTML('beforebegin', '<div id="login_error" role="alert"><p>' + error + '</p></div>');
	}
}

function startAuthentication(): void {
	const loginForm = document.getElementById('loginform') as HTMLFormElement;
	const publicKey = preparePublicKeyCredentialRequestOptions(tfa_webauthn.options);
	(
		navigator.credentials.get({
			publicKey,
		}) as Promise<PublicKeyCredential | null>
	)
		.then((credential) => {
			if (credential) {
				(document.getElementById('webauthn_response') as HTMLInputElement).value = JSON.stringify(
					preparePublicKeyCredential(credential),
				);
				loginForm.submit();
			} else {
				throw new Error(L_UNABLE_TO_GET_PK_CREDENTIAL);
			}
		})
		.catch((e: Error) => {
			const message = e instanceof DOMException ? decodeDOMException(e, true) : e.message;
			showError(message);
			(document.getElementById('webauthn-retry') as HTMLDivElement).removeAttribute('hidden');
		});
}

const callback = (): void => {
	const retryButton = document.querySelector('#webauthn-retry .button') as HTMLButtonElement;
	retryButton.addEventListener('click', (e) => {
		(document.getElementById('webauthn-retry') as HTMLDivElement).setAttribute('hidden', 'hidden');
		startAuthentication();
	});

	if ('credentials' in navigator) {
		startAuthentication();
	} else {
		showError(L_WEBAUTHN_NOT_SUPPORTED);
	}
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', callback);
} else {
	callback();
}
