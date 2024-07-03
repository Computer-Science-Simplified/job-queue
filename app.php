<?php

use Computersciencesimplified\JobQueue\Job\ExampleJob;
use Computersciencesimplified\JobQueue\Queue\ArrayQueue;

require('./vendor/autoload.php');

$queue = new \Computersciencesimplified\JobQueue\Queue\RedisQueue();

$queue->push(new ExampleJob('John'));
$queue->push(new ExampleJob('Joe'));
$queue->push(new ExampleJob('Jane'));
