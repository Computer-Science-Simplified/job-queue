<?php

namespace Tests\Job;

use Computersciencesimplified\JobQueue\Job\Job;

class TestJob extends Job
{
    public function __construct(public int $id)
    {
    }

    public function execute(): void
    {
    }
}
