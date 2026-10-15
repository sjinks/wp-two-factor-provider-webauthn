import type { CDPSession } from '@playwright/test';

export async function addVirtualAuthenticator(
	client: CDPSession,
	protocol: NonNullable<Parameters<typeof client.send<'WebAuthn.addVirtualAuthenticator'>>[ 1 ]>[ 'options' ][ 'protocol' ],
	transport: NonNullable<Parameters<typeof client.send<'WebAuthn.addVirtualAuthenticator'>>[ 1 ]>[ 'options' ][ 'transport' ],
): Promise<string> {
	await client.send( 'WebAuthn.enable' );

	const response = await client.send( 'WebAuthn.addVirtualAuthenticator', {
		options: {
			protocol,
			transport,
		},
	} );

	return response.authenticatorId;
}

export function removeVirtualAuthenticator( client: CDPSession, authenticatorId: string ): Promise<unknown> {
	return client.send( 'WebAuthn.removeVirtualAuthenticator', { authenticatorId } );
}

export async function getCredentials(
	client: CDPSession,
	authenticatorId: string,
) {
	const response = await client.send( 'WebAuthn.getCredentials', {
		authenticatorId,
	} );

	return response.credentials;
}

export async function getCredential(
	client: CDPSession,
	authenticatorId: string,
	credentialId: string,
) {
	const response = await client.send( 'WebAuthn.getCredential', {
		authenticatorId,
		credentialId,
	} );

	return response.credential;
}

export function clearCredentials( client: CDPSession, authenticatorId: string ): Promise<unknown> {
	return client.send( 'WebAuthn.clearCredentials', {
		authenticatorId,
	} );
}
