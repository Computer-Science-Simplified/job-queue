<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use Exception;

interface Queue
{
    public function push(Job $job): void;

    public function pop(): ?Job;

    public function isEmpty(): bool;

    public function failed(Job $job, Exception $ex): void;

    public function isDeadLetterQueueEmpty(): bool;

    public function popDeadLetterQueue(): ?Job;
}
