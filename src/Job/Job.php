<?php

namespace Computersciencesimplified\JobQueue\Job;

abstract class Job
{
    protected string $id;

    abstract public function execute(): void;

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
