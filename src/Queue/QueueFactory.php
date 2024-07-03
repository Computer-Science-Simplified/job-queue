<?php

namespace Computersciencesimplified\JobQueue\Queue;

use mysqli;
use Redis;

class QueueFactory
{
    public static function make(): Queue
    {
        return match ($_ENV['QUEUE_DRIVER']) {
            'redis' => self::makeRedisQueue(),
            'database' => self::makeDatabaseQueue(),
            'array' => new ArrayQueue(),
            default => new \UnexpectedValueException("Queue driver [" . $_ENV['QUEUE_DRIVER'] . "] is invalid"),
        };
    }

    private static function makeRedisQueue(): Queue
    {
        $redis = new Redis([
            'host' => $_ENV['REDIS_HOST'],
            'port' => (int) $_ENV['REDIS_PORT'],
        ]);

        return new RedisQueue($redis);
    }

    private static function makeDatabaseQueue(): Queue
    {
        $mysql = new mysqli(
            $_ENV['DB_HOST'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_DATABASE'],
            $_ENV['DB_PORT'],
        );

        return new DatabaseQueue($mysql);
    }
}
