<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use RuntimeException;
use Redis;

class RedisQueue extends Queue
{
    private Redis $redis;

    static string $REDIS_KEY = 'queue';

    public function __construct()
    {
        $this->redis = new Redis([
            'host' => '127.0.0.1',
            'port' => 63790,
            'connectTimeout' => 2.5,
        ]);
    }

    public function push(Job $job)
    {
        $result = $this->redis->rPush(self::$REDIS_KEY, serialize($job));

        if ($result === false) {
            throw new RuntimeException('Pushing failed');
        }
    }

    public function pop(): ?Job
    {
        $job = unserialize($this->redis->lPop(self::$REDIS_KEY));

        if ($job === false && !$this->isEmpty()) {
            throw new RuntimeException('Deserialization failed');
        }

        if ($job === false) {
            return null;
        }

        return $job;
    }

    public function isEmpty(): bool
    {
        return $this->redis->lLen(self::$REDIS_KEY) === 0;
    }
}
