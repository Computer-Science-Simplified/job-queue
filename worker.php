<?php

require('./vendor/autoload.php');

$worker = new \Computersciencesimplified\JobQueue\Worker\Worker('redis');

$worker->work();
