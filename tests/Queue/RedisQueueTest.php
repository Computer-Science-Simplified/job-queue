<?php

namespace Tests\Queue;

use Computersciencesimplified\JobQueue\Queue\Queue;
use Computersciencesimplified\JobQueue\Queue\RedisQueue;

class RedisQueueTest extends BaseQueueTest
{
    protected function createQueue(): Queue
    {
        return new RedisQueue();
    }
}
