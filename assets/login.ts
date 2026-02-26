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

function showError( error: string ): void {
	const el = document.getElementById( 'login_error' );
	el?.parentNode?.removeChild( el );

	const form = document.getElementById( 'loginform' );
	form?.insertAdjacentHTML( 'beforebegin', '<div id="login_error" role="alert"><p>' + error + '</p></div>' );
}

async function startAuthentication(): Promise<void> {
	const loginForm = document.getElementById( 'loginform' ) as HTMLFormElement | null;
	const publicKey = preparePublicKeyCredentialRequestOptions( tfa_webauthn.options );
	try {
		const credential = await navigator.credentials.get( { publicKey } ) as PublicKeyCredential | null;
		if ( credential ) {
			const input = document.getElementById( 'webauthn_response' ) as HTMLInputElement | null;
			if ( input && loginForm ) {
				input.value = JSON.stringify( preparePublicKeyCredential( credential ) );
				loginForm.submit();
			} else {
				// Must not happen
				self.location.reload();
			}
		} else {
			throw new Error( L_UNABLE_TO_GET_PK_CREDENTIAL );
		}
	} catch ( e ) {
		let message: string;
		if ( e instanceof DOMException ) {
			message = decodeDOMException( e, true );
		} else if ( e instanceof Error ) {
			message = e.message;
		} else {
			message = String( e );
		}

		showError( message );
	}
}

const callback = (): void => {
	const retryButton = document.querySelector( '#webauthn-retry .button' );
	retryButton?.addEventListener( 'click', () => void startAuthentication() );

	if ( 'credentials' in navigator ) {
		if ( ! navigator.webdriver ) {
			void startAuthentication();
		}
	} else {
		showError( L_WEBAUTHN_NOT_SUPPORTED );
	}
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', callback );
} else {
	callback();
}
