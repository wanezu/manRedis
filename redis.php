<?php
//实例化
$redis = new Redis();
//连接服务器
$redis->connect("localhost","6379");

$redis->auth('wuyonghao');