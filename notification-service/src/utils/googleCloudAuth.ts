const { GoogleAuth } = require('google-auth-library');

export async function getGCPIdentityToken() {
    console.log("Autenticando no Google Cloud para resgatar token....");
    let url = process.env.MAILER_SERVICE_URL;

    const auth = new GoogleAuth({
        keyFile: './key.json',
    });

    const client = await auth.getIdTokenClient(process.env.MAILER_SERVICE_URL);
    let token = await client.idTokenProvider.fetchIdToken(url);
    if (token)
        console.log("Token resgatado com sucesso \n")
    return token
}