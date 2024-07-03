<?php

namespace Computersciencesimplified\JobQueue\Worker;

use Computersciencesimplified\JobQueue\Queue\Queue;
use Computersciencesimplified\JobQueue\Queue\RedisQueue;

class Worker
{
    private Queue $queue;

    public function __construct(string $queueDriver)
    {
        $this->queue = match ($queueDriver) {
            'redis' => new RedisQueue()
        };
    }

    public function work(): void
    {
        while (true) {
            sleep(1);

            if (!$this->queue->isEmpty()) {
                $job = $this->queue->pop();

                $job->execute();
            }
        }
    }
}
