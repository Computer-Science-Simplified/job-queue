<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;

abstract class Queue
{
    abstract public function push(Job  $job);

    abstract public function pop(): ?Job;

    abstract public function isEmpty(): bool;
}
