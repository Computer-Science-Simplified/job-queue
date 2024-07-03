<?php

namespace Tests\Queue;

use Computersciencesimplified\JobQueue\Queue\ArrayQueue;
use Computersciencesimplified\JobQueue\Queue\Queue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Job\TestJob;

abstract class BaseQueueTest extends TestCase
{
    abstract protected function createQueue(): Queue;

    #[Test]
    public function it_should_behave_as_a_fifo_queue()
    {
        $queue = $this->createQueue();

        $queue->push(new TestJob(1));
        $queue->push(new TestJob(2));
        $queue->push(new TestJob(3));

        $this->assertSame(1, ($queue->pop())->id);
        $this->assertSame(2, ($queue->pop())->id);
        $this->assertSame(3, ($queue->pop())->id);

        $this->assertTrue($queue->isEmpty());

        $this->assertNull($queue->pop());
    }
}
