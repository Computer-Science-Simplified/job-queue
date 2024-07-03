<?php

use Computersciencesimplified\JobQueue\Job\ExampleJob;
use Computersciencesimplified\JobQueue\Queue\QueueFactory;

require('./vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$queue = QueueFactory::make();

$queue->push(new ExampleJob('John'));
$queue->push(new ExampleJob('Joe'));
$queue->push(new ExampleJob('Jane'));
