<?php

namespace Computersciencesimplified\JobQueue\Job;

class ExampleJob implements Job
{
    public function __construct(private string $name)
    {
    }

    public function execute(): void
    {
        var_dump('Hi ' . $this->name);
    }
}
