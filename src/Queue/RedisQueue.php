<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use RuntimeException;
use Redis;

class RedisQueue implements Queue
{
    static string $REDIS_KEY = 'queue';

    public function __construct(private Redis $redis)
    {
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
        $unserializedJob = $this->redis->lPop(self::$REDIS_KEY);

        if (!$unserializedJob) {
            return null;
        }

        $job = unserialize($unserializedJob);

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
