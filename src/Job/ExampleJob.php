<?php

namespace Computersciencesimplified\JobQueue\Job;

use Exception;

class ExampleJob extends Job
{
    public function __construct(private string $name)
    {
    }

    public function execute(): void
    {
        if ($this->name === 'Joe') {
            sleep(2);

            throw new Exception('Unable to process Joe');
        }
    }
}
