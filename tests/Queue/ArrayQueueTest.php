<?php

namespace Tests\Queue;

use Computersciencesimplified\JobQueue\Queue\ArrayQueue;
use Computersciencesimplified\JobQueue\Queue\Queue;

class ArrayQueueTest extends BaseQueueTest
{
    protected function createQueue(): Queue
    {
        return new ArrayQueue();
    }
}
