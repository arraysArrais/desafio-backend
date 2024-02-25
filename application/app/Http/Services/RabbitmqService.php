<?php

namespace App\Http\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqService
{
    public function sendMsg($msg)
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'),
            env('RABBITMQ_PASSWORD'),
            env('RABBITMQ_VHOST')
        );
        $channel = $connection->channel();

        //fila
        $channel->queue_declare('notificacao', false, true, false, false);

        //envio
        $channel->basic_publish(new AMQPMessage($msg), 'amq.fanout', 'n1');

        $channel->close();
        $connection->close();
    }
}
