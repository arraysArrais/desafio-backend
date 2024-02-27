import { Transaction } from "./types";

export async function sendMailRequest(serviceURL: string, gcpToken: string, content: Transaction) {
    let currencyFormatOption = {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
        useGrouping: false,
    }

    let req = await fetch(serviceURL, {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + gcpToken
        },
        body: JSON.stringify({
            to: content.receiver.email,
            subject: "Você recebeu um novo pagamento!",
            message: `Olá ${content.receiver.name}!\nVocê recebeu um pagamento no valor de R$ ${content.value.toLocaleString('pt-BR', currencyFormatOption)} do usuário ${content.sender.name}.\nSeu saldo é de R$ ${Number(content.receiver.balance).toLocaleString('pt-BR', currencyFormatOption)}`,
        }),
    });

    return req; //será resolvida externamente
}