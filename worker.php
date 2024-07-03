<?php

require('./vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$worker = new \Computersciencesimplified\JobQueue\Worker\Worker();

if ($argc === 2 && $argv[1] === 'retry') {
    $worker->retry();
} else {
    $worker->work();
}
