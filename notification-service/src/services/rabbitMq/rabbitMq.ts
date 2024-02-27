import { Connection, Channel, connect } from "amqplib";
import { sendMailRequest } from "../mailService/mail";

export default class RabbitMQClient {
    public connection: Connection;
    public channel: Channel;

    constructor(private url: string) { }

    async openConnection() {
        this.connection = await connect(this.url);
        this.channel = await this.connection.createChannel();

        if (this.connection) {
            console.log("Conexão iniciada com o servidor do RabbitMQ" + "\n",
                "Cluster: ", this.connection.connection.serverProperties.cluster_name + "\n",
                "Platform: ", this.connection.connection.serverProperties.platform + "\n",
                "Product: ", this.connection.connection.serverProperties.platform + "\n",
                "Version: ", this.connection.connection.serverProperties.version + "\n",)
            return this.connection;
        }
    }

    sendMsg(queue: string, msg: string) {
        return this.channel.sendToQueue(queue, Buffer.from(msg));
    }

    async consumeMsgs(queue: string, gcpToken: any) {
        console.log(`Escutando por novas mensagens na fila "${process.env.RABBITMQ_QUEUE}"....`);
        return this.channel.consume(queue, async (message) => {
            console.log("Mensagem recebida: ", message.content.toString())

            console.log("Enviando e-mails de notificação...")
            let req = await sendMailRequest(process.env.MAILER_SERVICE_URL, gcpToken, JSON.parse(message.content.toString()))

            console.log("Resposta do serviço externo: " + req.status, await req.text()); //em casos de erro o GCP retorna a resposta em XML
            //ack em caso de sucesso
            if (req.status == 200) {
                this.channel.ack(message);
            }
        });

    }

    async closeConnection(): Promise<void> {
        console.log("Encerrado conexão...")
        this.channel = await this.channel.close();
        this.connection = await this.connection.close();
    }
}