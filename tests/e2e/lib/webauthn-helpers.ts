import type { CDPSession } from '@playwright/test';
import type { Protocol } from 'playwright-core/types/protocol';

export async function addVirtualAuthenticator(
	client: CDPSession,
	protocol: Protocol.WebAuthn.AuthenticatorProtocol,
	transport: Protocol.WebAuthn.AuthenticatorTransport,
): Promise<string> {
	await client.send('WebAuthn.enable');

	const response = await client.send('WebAuthn.addVirtualAuthenticator', {
		options: {
			protocol,
			transport,
		},
	});

	return response.authenticatorId;
}

export function removeVirtualAuthenticator(client: CDPSession, authenticatorId: string): Promise<unknown> {
	return client.send('WebAuthn.removeVirtualAuthenticator', { authenticatorId });
}

export async function getCredentials(
	client: CDPSession,
	authenticatorId: string,
): Promise<Protocol.WebAuthn.Credential[]> {
	const response: Protocol.WebAuthn.getCredentialsReturnValue = await client.send('WebAuthn.getCredentials', {
		authenticatorId,
	});

	return response.credentials;
}

export async function getCredential(
	client: CDPSession,
	authenticatorId: string,
	credentialId: string,
): Promise<Protocol.WebAuthn.Credential> {
	const response: Protocol.WebAuthn.getCredentialReturnValue = await client.send('WebAuthn.getCredential', {
		authenticatorId,
		credentialId,
	});

	return response.credential;
}

export function clearCredentials(client: CDPSession, authenticatorId: string): Promise<unknown> {
	return client.send('WebAuthn.clearCredentials', {
		authenticatorId,
	});
}
