<?php

namespace Tests\Queue;

use Computersciencesimplified\JobQueue\Queue\Queue;
use Computersciencesimplified\JobQueue\Queue\QueueFactory;

class DatabaseQueueTest extends BaseQueueTest
{
    protected function createQueue(): Queue
    {
        return QueueFactory::make();
    }
}
