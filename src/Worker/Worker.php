<?php

namespace Computersciencesimplified\JobQueue\Worker;

use Computersciencesimplified\JobQueue\Queue\Queue;
use Computersciencesimplified\JobQueue\Queue\QueueFactory;
use Exception;

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

                try {
                    echo date('Y-m-d H:i:s') . "\t" . $job::class . "\t\t" . "PROCESSING\n";

                    $job?->execute();

                    echo date('Y-m-d H:i:s') . "\t" . $job::class . "\t\t" . "DONE\n";
                } catch (Exception $ex) {
                    $this->queue->failed($job, $ex);

                    echo date('Y-m-d H:i:s') . "\t" . $job::class . "\t\t" . "FAILED\n";
                }
            }
        }
    }
}
