import {
	L_NOT_ALLOWED_ERROR,
	L_SECURITY_ERROR,
	L_NOT_SUPPORTED_ERROR,
	L_ABORT_ERROR,
	L_UNKNOWN_KEY,
	L_KEY_ALREADY_REGISTERED,
} from './lang';

type Convert<O extends object> = {
	[K in keyof O]: O[K] extends BufferSource | null ? string : O[K] extends object ? Convert<O[K]> : O[K];
};

export interface PublicKeyCredentialCreationOptionsPlain {
	attestation?: AttestationConveyancePreference;
	authenticatorSelection?: AuthenticatorSelectionCriteria;
	challenge: string;
	excludeCredentials?: Convert<PublicKeyCredentialDescriptor>[];
	extensions?: AuthenticationExtensionsClientInputs;
	pubKeyCredParams: PublicKeyCredentialParameters[];
	rp: PublicKeyCredentialRpEntity;
	timeout?: number;
	user: Convert<PublicKeyCredentialUserEntity>;
}

export interface PublicKeyCredentialRequestOptionsPlain {
	allowCredentials?: Convert<PublicKeyCredentialDescriptor>[];
	challenge: string;
	extensions?: AuthenticationExtensionsClientInputs;
	rpId?: string;
	timeout?: number;
	userVerification?: UserVerificationRequirement;
}

export interface PublicKeyCredentialPlain extends Credential {
	rawId: string;
	response: Partial<Convert<AuthenticatorAttestationResponse & AuthenticatorAssertionResponse>>;
	clientExtensionResults: AuthenticationExtensionsClientOutputs;
}

function arrayToBase64String(a: Uint8Array): string {
	return window.btoa(String.fromCharCode(...a));
}

function base64UrlDecode(input: string): string {
	return window.atob(input.replace(/-/g, '+').replace(/_/g, '/') + '='.repeat(3 - ((3 + input.length) % 4)));
}

function stringToBuffer(s: string): ArrayBuffer {
	return Uint8Array.from(s, (c) => c.charCodeAt(0));
}

export function preparePublicKeyCreationOptions(
	publicKey: PublicKeyCredentialCreationOptionsPlain,
): PublicKeyCredentialCreationOptions {
	return {
		...publicKey,
		user: {
			...publicKey.user,
			id: stringToBuffer(base64UrlDecode(publicKey.user.id)),
		},
		challenge: stringToBuffer(base64UrlDecode(publicKey.challenge)),
		excludeCredentials: publicKey.excludeCredentials?.map(
			(data: Convert<PublicKeyCredentialDescriptor>): PublicKeyCredentialDescriptor => ({
				...data,
				id: stringToBuffer(base64UrlDecode(data.id)),
			}),
		),
	};
}

export function preparePublicKeyCredentialRequestOptions(
	publicKey: PublicKeyCredentialRequestOptionsPlain,
): PublicKeyCredentialRequestOptions {
	return {
		...publicKey,
		challenge: stringToBuffer(base64UrlDecode(publicKey.challenge)),
		allowCredentials: publicKey.allowCredentials?.map(
			(data: Convert<PublicKeyCredentialDescriptor>): PublicKeyCredentialDescriptor => ({
				...data,
				id: stringToBuffer(base64UrlDecode(data.id)),
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
