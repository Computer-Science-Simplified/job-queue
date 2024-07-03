<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use Exception;
use RuntimeException;
use Redis;

class RedisQueue implements Queue
{
    private const QUEUE_KEY = 'queue';

    private const DEAD_LETTER_QUEUE_KEY = 'failed_jobs';

    public function __construct(private Redis $redis)
    {
    }

    public function push(Job $job)
    {
        $result = $this->redis->rPush(self::QUEUE_KEY, serialize($job));

        if ($result === false) {
            throw new RuntimeException('Pushing failed');
        }
    }

    public function pop(): ?Job
    {
        $unserializedJob = $this->redis->lPop(self::QUEUE_KEY);

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
        return $this->redis->lLen(self::QUEUE_KEY) === 0;
    }

    #[\Override] public function failed(Job $job, Exception $ex): void
    {
        $this->redis->rPush(self::DEAD_LETTER_QUEUE_KEY, json_encode([
            'job' => serialize($job),
            'exception' => serialize($ex),
            'message' => $ex->getMessage(),
            'failed_at' => date('Y-m-d H:i:s'),
        ]));
    }
}
