<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use Exception;
use RuntimeException;
use Redis;

class RedisQueue implements Queue
{
    public function __construct(private Redis $redis)
    {
    }

    public function push(Job $job): void
    {
        $job->setId(uniqid());

        $result = $this->redis->rPush($this->getQueueName(), json_encode([
            'payload' => serialize($job),
        ]));

        if ($result === false) {
            throw new RuntimeException('Pushing failed');
        }
    }

    public function pop(): ?Job
    {
        return $this->popFrom($this->getQueueName());
    }

    public function isEmpty(): bool
    {
        return $this->redis->lLen($this->getQueueName()) === 0;
    }

    #[\Override] public function failed(Job $job, Exception $ex): void
    {
        $this->redis->rPush($this->getDeadLetterQueueName(), json_encode([
            'payload' => serialize($job),
            'job_id' => $job->getId(),
            'exception' => serialize($ex),
            'message' => $ex->getMessage(),
            'failed_at' => date('Y-m-d H:i:s'),
        ]));
    }

    #[\Override] public function isDeadLetterQueueEmpty(): bool
    {
        return $this->redis->lLen($this->getDeadLetterQueueName()) === 0;
    }

    #[\Override] public function popDeadLetterQueue(): ?Job
    {
        return $this->popFrom($this->getDeadLetterQueueName());
    }

    private function popFrom(string $queue): ?Job
    {
        $data = $this->redis->lPop($queue);

        if (!$data) {
            return null;
        }

        $decoded = json_decode($data, true);

        if (!$decoded) {
            throw new RuntimeException('Decoding failed');
        }

        $job = unserialize($decoded['payload']);

        if ($job === false) {
            throw new RuntimeException('Deserialization failed');
        }

        return $job;
    }

    #[\Override] public function getQueueName(): string
    {
        return 'queue';
    }

    #[\Override] public function getDeadLetterQueueName(): string
    {
        return 'dead_letter_queue';
    }
}
