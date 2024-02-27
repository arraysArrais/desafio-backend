import { Module } from '@nestjs/common';
import { AppController } from './app.controller';
import { ConfigModule } from '@nestjs/config';
import { ScheduleModule } from '@nestjs/schedule';
import RabbitMQClient from './services/rabbitMq/rabbitMq';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
    }),
    ScheduleModule.forRoot(),
  ],
  controllers: [AppController],
  providers: [{
    provide: RabbitMQClient,
    useFactory: () => new RabbitMQClient(process.env.RABBITMQ_URL)
  }],
})
export class AppModule {}
