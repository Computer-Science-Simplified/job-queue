<?php

namespace Computersciencesimplified\JobQueue\Queue;

use Computersciencesimplified\JobQueue\Job\Job;
use mysqli;
use RuntimeException;

class DatabaseQueue extends Queue
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
        $sql = "select id, payload from jobs order by id asc limit 1";

        $result = $this->mysql->query($sql);

        $row = $result->fetch_assoc();

        $job = unserialize($row['payload']);

        if ($job === false && !$this->isEmpty()) {
            throw new RuntimeException('Deserialization failed');
        }

        if ($job === false) {
            return null;
        }

        $sql = "delete from jobs where id = ?";

        $query = $this->mysql->prepare($sql);

        $query->bind_param("i", $row['id']);

        $query->execute();

        return $job;
    }

    public function isEmpty(): bool
    {
        $sql = "select count(id) as count from jobs";

        $result = $this->mysql->query($sql);

        $row = $result->fetch_assoc();

        return ((int) $row['count']) === 0;
    }
}
