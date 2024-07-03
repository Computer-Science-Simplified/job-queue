<?php

namespace Tests\Queue;

use Computersciencesimplified\JobQueue\Queue\DatabaseQueue;
use Computersciencesimplified\JobQueue\Queue\Queue;

class DatabaseQueueTest extends BaseQueueTest
{
    protected function createQueue(): Queue
    {
        return new DatabaseQueue();
    }
}
