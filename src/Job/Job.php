<?php

namespace Computersciencesimplified\JobQueue\Job;

interface Job
{
    public function execute(): void;
}
