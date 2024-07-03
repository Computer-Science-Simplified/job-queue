<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;

class ArrayQueue extends Queue
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
}
