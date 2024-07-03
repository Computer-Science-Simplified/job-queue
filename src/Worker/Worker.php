<?php

namespace Computersciencesimplified\JobQueue\Worker;

use Computersciencesimplified\JobQueue\Queue\Queue;
use Computersciencesimplified\JobQueue\Queue\QueueFactory;

class Worker
{
    private Queue $queue;

    public function __construct()
    {
        $this->queue = QueueFactory::make();
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
