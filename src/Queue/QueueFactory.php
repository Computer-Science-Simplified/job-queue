<?php

namespace Computersciencesimplified\JobQueue\Queue;

class QueueFactory
{
    public static function make(): Queue
    {
        return match ($_ENV['QUEUE_DRIVER']) {
            'redis' => new RedisQueue(),
            'database' => new DatabaseQueue(),
            'array' => new ArrayQueue(),
            default => new \UnexpectedValueException("Queue driver [" . $_ENV['QUEUE_DRIVER'] . "] is invalid"),
        };
    }
}
