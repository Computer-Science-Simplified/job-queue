<?php

namespace Tests\Queue;

use Computersciencesimplified\JobQueue\Queue\Queue;
use Dotenv\Dotenv;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Job\TestJob;

abstract class BaseQueueTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..', '.env.testing');

        $dotenv->load();
    }

    abstract protected function createQueue(): Queue;

    #[Test]
    public function it_should_behave_as_a_fifo_queue()
    {
        $queue = $this->createQueue();

        $queue->push(new TestJob('first'));
        $queue->push(new TestJob('second'));
        $queue->push(new TestJob('third'));

        $this->assertSame('first', ($queue->pop())->title);
        $this->assertSame('second', ($queue->pop())->title);
        $this->assertSame('third', ($queue->pop())->title);

        $this->assertTrue($queue->isEmpty());

        $this->assertNull($queue->pop());
    }
}
