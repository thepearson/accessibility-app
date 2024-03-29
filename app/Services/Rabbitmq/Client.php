<?php

namespace App\Services\Rabbitmq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
 
class Client
{
    public function __construct(
        protected string $host, 
        protected string $port, 
        protected string $user, 
        protected string $password
    ) {}

    /**
     * Connect to the queue
     */
    public function connect() 
    {
        $this->connection = new AMQPStreamConnection(
            $this->host, 
            $this->port, 
            $this->user, 
            $this->password
        );
        $this->channel = $this->connection->channel();
    }


    /**
     * Send a message to the queue
     */
    public function message($payload, $queue)
    {
        $this->channel->queue_declare($queue, false, true, false, false);
        $message = new AMQPMessage($payload);
        $this->channel->basic_publish($message, '', $queue);
    }

    /**
     * Close the connection
     */
    public function close()
    {
        $this->channel->close();
    }
}
