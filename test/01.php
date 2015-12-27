<?php
$redis = new Redis();
//连接服务器
$redis->connect("localhost","6379");

var_dump($redis->info());
var_dump($redis->info('COMMANDSTATS'));
var_dump($redis->info('CPU'));