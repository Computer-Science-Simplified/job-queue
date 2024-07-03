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
            die("Connection failed: " . $this->mysql->connect_error);
        }
    }

    public function push(Job $job)
    {
        $payload = serialize($job);

        $createdAt = date('Y-m-d H:i:s');

        $sql = "INSERT INTO jobs(payload, created_at) values(?, ?)";

        $query = $this->mysql->prepare($sql);

        $query->bind_param("ss", $payload, $createdAt);

        if (!$query->execute()) {
            throw new RuntimeException('Unable to execute query');
        }
    }

    public function pop(): ?Job
    {
        $result = $this->mysql->query("select id, payload from jobs order by id asc limit 1");

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

        $query = $this->mysql->prepare("delete from jobs where id = ?");

        $query->bind_param("i", $row['id']);

        $query->execute();

        return $job;
    }

    public function isEmpty(): bool
    {
        $result = $this->mysql->query("select count(id) as count from jobs");

        $row = $result->fetch_assoc();

        return ((int) $row['count']) === 0;
    }

    #[\Override] public function failed(Job $job, Exception $ex): void
    {
        // TODO: Implement failed() method.
    }
}
