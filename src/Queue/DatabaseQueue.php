<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use Exception;
use mysqli;
use RuntimeException;

class DatabaseQueue implements Queue
{
    public function __construct(private mysqli $mysql)
    {
        if ($this->mysql->connect_error) {
            throw new Exception("Connection failed: " . $this->mysql->connect_error);
        }
    }

    public function push(Job $job): void
    {
        $job->setId(uniqid());

        $payload = serialize($job);

        $createdAt = date('Y-m-d H:i:s');

        $sql = "insert into {$this->getQueueName()}(payload, created_at) values(?, ?)";

        $query = $this->mysql->prepare($sql);

        $query->bind_param("ss", $payload, $createdAt);

        if (!$query->execute()) {
            throw new RuntimeException('Unable to execute query');
        }
    }

    public function pop(): ?Job
    {
        return $this->popFrom($this->getQueueName());
    }

    public function isEmpty(): bool
    {
        $result = $this->mysql->query("select count(id) as count from " . $this->getQueueName());

        $row = $result->fetch_assoc();

        return ((int) $row['count']) === 0;
    }

    #[\Override] public function failed(Job $job, Exception $ex): void
    {
        $sql = "insert into {$this->getDeadLetterQueueName()}(job_id, payload, exception, message, failed_at) values(?, ?, ?, ?, ?)";

        $query = $this->mysql->prepare($sql);

        $serializedJob = serialize($job);

        $serializedEx = serialize($ex);

        $message = $ex->getMessage();

        $failedAt = date('Y-m-d H:i:s');

        $jobId = $job->getId();

        $query->bind_param("sssss",  $jobId,$serializedJob, $serializedEx, $message, $failedAt);

        if (!$query->execute()) {
            throw new RuntimeException('Unable to execute query');
        }
    }

    #[\Override] public function isDeadLetterQueueEmpty(): bool
    {
        $result = $this->mysql->query("select count(id) as count from " . $this->getDeadLetterQueueName());

        $row = $result->fetch_assoc();

        return ((int) $row['count']) === 0;
    }

    #[\Override] public function popDeadLetterQueue(): ?Job
    {
        return $this->popFrom($this->getDeadLetterQueueName());
    }

    private function popFrom(string $table): ?Job
    {
        $result = $this->mysql->query("select id, payload from $table order by id asc limit 1");

        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        $job = unserialize($row['payload']);

        if ($job === false) {
            throw new RuntimeException('Deserialization failed');
        }

        $query = $this->mysql->prepare("delete from $table where id = ?");

        $query->bind_param("i", $row['id']);

        $query->execute();

        return $job;
    }

    #[\Override] public function getQueueName(): string
    {
        return 'jobs';
    }

    #[\Override] public function getDeadLetterQueueName(): string
    {
        return 'failed_jobs';
    }
}
