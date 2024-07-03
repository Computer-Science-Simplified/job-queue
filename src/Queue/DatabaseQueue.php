<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use Exception;
use mysqli;
use RuntimeException;

class DatabaseQueue implements Queue
{
    private const QUEUE_TABLE = 'jobs';

    private const DEAD_LETTER_QUEUE_TABLE = 'failed_jobs';

    public function __construct(private mysqli $mysql)
    {
        if ($this->mysql->connect_error) {
            die("Connection failed: " . $this->mysql->connect_error);
        }
    }

    public function push(Job $job)
    {
        $job->setId(uniqid());

        $payload = serialize($job);

        $createdAt = date('Y-m-d H:i:s');

        $sql = "insert into jobs(payload, created_at) values(?, ?)";

        $query = $this->mysql->prepare($sql);

        $query->bind_param("ss", $payload, $createdAt);

        if (!$query->execute()) {
            throw new RuntimeException('Unable to execute query');
        }
    }

    public function pop(): ?Job
    {
        return $this->popFrom(self::QUEUE_TABLE);
    }

    public function isEmpty(): bool
    {
        $result = $this->mysql->query("select count(id) as count from jobs");

        $row = $result->fetch_assoc();

        return ((int) $row['count']) === 0;
    }

    #[\Override] public function failed(Job $job, Exception $ex): void
    {
        $sql = "insert into failed_jobs(job_id, payload, exception, message, failed_at) values(?, ?, ?, ?, ?)";

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
        $result = $this->mysql->query("select count(id) as count from failed_jobs");

        $row = $result->fetch_assoc();

        return ((int) $row['count']) === 0;
    }

    #[\Override] public function popDeadLetterQueue(): ?Job
    {
        return $this->popFrom(self::DEAD_LETTER_QUEUE_TABLE);
    }

    private function popFrom(string $table): ?Job
    {
        $result = $this->mysql->query("select id, payload from $table order by id asc limit 1");

        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        $job = unserialize($row['payload']);

        if ($job === false && !$this->isEmpty()) {
            throw new RuntimeException('Deserialization failed');
        }

        if ($job === false) {
            return null;
        }

        $query = $this->mysql->prepare("delete from $table where id = ?");

        $query->bind_param("i", $row['id']);

        $query->execute();

        return $job;
    }
}
