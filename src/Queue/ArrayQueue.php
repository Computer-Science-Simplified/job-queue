<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use Exception;

class ArrayQueue implements Queue
{
    private array $jobs = [];

    public function push(Job $job)
    {
        $this->jobs[] = $job;
    }

    public function pop(): ?Job
    {
        return array_shift($this->jobs);
    }

    public function isEmpty(): bool
    {
        return count($this->jobs) === 0;
    }

    #[\Override] public function failed(Job $job, Exception $ex): void
    {
        throw $ex;
    }
}
