import {
	L_NOT_ALLOWED_ERROR,
	L_SECURITY_ERROR,
	L_NOT_SUPPORTED_ERROR,
	L_ABORT_ERROR,
	L_UNKNOWN_KEY,
	L_KEY_ALREADY_REGISTERED,
} from './lang';
interface PublicKeyCredentialUserEntityPlain extends PublicKeyCredentialEntity {
	displayName: string;
	id: string;
}

interface PublicKeyCredentialDescriptorPlain {
	id: string;
	transports?: AuthenticatorTransport[];
	type: PublicKeyCredentialType;
}

export interface PublicKeyCredentialCreationOptionsPlain {
	attestation?: AttestationConveyancePreference;
	authenticatorSelection?: AuthenticatorSelectionCriteria;
	challenge: string;
	excludeCredentials?: PublicKeyCredentialDescriptorPlain[];
	extensions?: AuthenticationExtensionsClientInputs;
	pubKeyCredParams: PublicKeyCredentialParameters[];
	rp: PublicKeyCredentialRpEntity;
	timeout?: number;
	user: PublicKeyCredentialUserEntityPlain;
}

export interface PublicKeyCredentialRequestOptionsPlain {
	allowCredentials?: PublicKeyCredentialDescriptorPlain[];
	challenge: string;
	extensions?: AuthenticationExtensionsClientInputs;
	rpId?: string;
	timeout?: number;
	userVerification?: UserVerificationRequirement;
}

interface AuthenticatorResponsePlain {
	clientDataJSON: string;
}

interface AuthenticatorAttestationResponsePlain extends AuthenticatorResponsePlain {
	attestationObject: string;
}

interface AuthenticatorAssertionResponsePlain extends AuthenticatorResponsePlain {
	authenticatorData: string;
	signature: string;
	userHandle: string | null | undefined;
}

export interface PublicKeyCredentialPlain extends Credential {
	rawId: string;
	response: Partial<AuthenticatorAttestationResponsePlain & AuthenticatorAssertionResponsePlain>;
	clientExtensionResults: AuthenticationExtensionsClientOutputs;
}

export function arrayToBase64String(a: Uint8Array): string {
	return window.btoa(String.fromCharCode(...a));
}

export function base64UrlDecode(input: string): string {
	return window.atob(input.replace(/-/g, '+').replace(/_/g, '/') + '='.repeat(3 - ((3 + input.length) % 4)));
}

export function stringToBufferMapper(c: string): number {
	return c.charCodeAt(0);
}

export function preparePublicKeyCreationOptions(
	publicKey: PublicKeyCredentialCreationOptionsPlain,
): PublicKeyCredentialCreationOptions {
	return {
		...publicKey,
		user: {
			...publicKey.user,
			id: Uint8Array.from(base64UrlDecode(publicKey.user.id), stringToBufferMapper),
		},
		challenge: Uint8Array.from(base64UrlDecode(publicKey.challenge), stringToBufferMapper),
		excludeCredentials: publicKey.excludeCredentials?.map(
			(data: PublicKeyCredentialDescriptorPlain): PublicKeyCredentialDescriptor => ({
				...data,
				id: Uint8Array.from(base64UrlDecode(data.id), stringToBufferMapper),
			}),
		),
	};
}

export function preparePublicKeyCredentialRequestOptions(
	publicKey: PublicKeyCredentialRequestOptionsPlain,
): PublicKeyCredentialRequestOptions {
	return {
		...publicKey,
		challenge: Uint8Array.from(base64UrlDecode(publicKey.challenge), stringToBufferMapper).buffer,
		allowCredentials: publicKey.allowCredentials?.map(
			(data: PublicKeyCredentialDescriptorPlain): PublicKeyCredentialDescriptor => ({
				...data,
				id: Uint8Array.from(base64UrlDecode(data.id), stringToBufferMapper).buffer,
			}),
		),
	};
}

export function preparePublicKeyCredential(data: PublicKeyCredential): PublicKeyCredentialPlain {
	const response = data.response as AuthenticatorAssertionResponse | AuthenticatorAttestationResponse;
	return {
		id: data.id,
		type: data.type,
		rawId: arrayToBase64String(new Uint8Array(data.rawId)),
		clientExtensionResults: data.getClientExtensionResults(),
		response: {
			attestationObject:
				'attestationObject' in response
					? arrayToBase64String(new Uint8Array(response.attestationObject))
					: undefined,
			authenticatorData:
				'authenticatorData' in response
					? arrayToBase64String(new Uint8Array(response.authenticatorData))
					: undefined,
			signature: 'signature' in response ? arrayToBase64String(new Uint8Array(response.signature)) : undefined,
			userHandle:
				'userHandle' in response && response.userHandle
					? arrayToBase64String(new Uint8Array(response.userHandle))
					: undefined,
			clientDataJSON: arrayToBase64String(new Uint8Array(data.response.clientDataJSON)),
		},
	};
}

export function decodeDOMException(e: DOMException, isAuth: boolean): string {
	switch (e.name) {
		case 'NotAllowedError':
			return L_NOT_ALLOWED_ERROR;

		case 'SecurityError':
			return L_SECURITY_ERROR;

		case 'NotSupportedError':
			return L_NOT_SUPPORTED_ERROR;

		case 'AbortError':
			return L_ABORT_ERROR;

		case 'InvalidStateError':
			return isAuth ? L_UNKNOWN_KEY : L_KEY_ALREADY_REGISTERED;

		default:
			return e.message;
	}
}
