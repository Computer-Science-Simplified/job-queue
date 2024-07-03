<?php

namespace Tests\Queue;

use Computersciencesimplified\JobQueue\Queue\ArrayQueue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Job\TestJob;

class ArrayQueueTest extends TestCase
{
    #[Test]
    public function it_should_behave_as_a_fifo_queue()
    {
        $queue = new ArrayQueue();

        $queue->push(new TestJob(1));
        $queue->push(new TestJob(2));
        $queue->push(new TestJob(3));

        $this->assertSame(1, ($queue->pop())->id);
        $this->assertSame(2, ($queue->pop())->id);
        $this->assertSame(3, ($queue->pop())->id);

        $this->assertTrue($queue->isEmpty());
    }
}
