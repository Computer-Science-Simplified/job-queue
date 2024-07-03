<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;

interface Queue
{
    public function push(Job  $job);

    public function pop(): ?Job;

    public function isEmpty(): bool;
}
