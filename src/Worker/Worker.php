<?php

namespace Computersciencesimplified\JobQueue\Worker;

use Computersciencesimplified\JobQueue\Job\Job;
use Computersciencesimplified\JobQueue\Queue\Queue;
use Computersciencesimplified\JobQueue\Queue\QueueFactory;
use Exception;

class Worker
{
    private Queue $queue;

    public function __construct(QueueFactory $factory)
    {
        $this->queue = $factory->make();
    }

    public function work(): void
    {
        while (true) {
            sleep(1);

            if (!$this->queue->isEmpty()) {
                $job = $this->queue->pop();

                $this->executeJob($job);
            }
        }
    }

    public function retry(): void
    {
        while (true) {
            sleep(1);

            if (!$this->queue->isDeadLetterQueueEmpty()) {
                $job = $this->queue->popDeadLetterQueue();

                $this->executeJob($job);
            } else {
                return;
            }
        }
    }

    private function executeJob(?Job $job): void
    {
        try {
            $this->printStatus($job, 'PROCESSING');

            $job?->execute();

            $this->printStatus($job, 'DONE');
        } catch (Exception $ex) {
            $this->queue->failed($job, $ex);

            $this->printStatus($job, 'FAILED');
        }
    }

    private function printStatus(Job $job, string $status): void
    {
        echo date('Y-m-d H:i:s') . "\t" . $job::class . "\t\t" . "$status\n";
    }
}
