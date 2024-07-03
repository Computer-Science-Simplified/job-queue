<?php

use Computersciencesimplified\JobQueue\Queue\QueueFactory;
use Computersciencesimplified\JobQueue\Worker\Worker;

require('./vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$worker = new Worker(new QueueFactory());

if ($argc === 2 && $argv[1] === 'retry') {
    $worker->retry();
} else {
    $worker->work();
}
