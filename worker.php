<?php

require('./vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$worker = new \Computersciencesimplified\JobQueue\Worker\Worker();

$worker->work();
