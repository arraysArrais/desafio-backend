import { Controller, Get } from '@nestjs/common';
import RabbitMQClient from './services/rabbitMq/rabbitMq';
import { getGCPIdentityToken } from './utils/googleCloudAuth';
import { Cron } from '@nestjs/schedule';

@Controller()
export class AppController {
  constructor(
    private readonly rabbitMQClient: RabbitMQClient
  ) { }


  @Cron('5 * * * * *')
  @Get()
  async entryPoint() {
    await this.rabbitMQClient.openConnection();
    let token = await getGCPIdentityToken();
    await this.rabbitMQClient.consumeMsgs(process.env.RABBITMQ_QUEUE, token);
  }
}



