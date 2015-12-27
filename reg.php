<?php
require('./redis.php');
$username=isset($_POST['username'])?$_POST['username']:false;
$password = isset($_POST['password'])?md5($_POST['password']):false;
$age = isset($_POST['age'])?$_POST['age']:false;
$uid = $redis->incr("userid");
$redis->hmset("user:" . $uid,array("uid"=>$uid,"username"=>$username,"password"=>$password,"age"=>$age));
$redis->rpush("uid",$uid);
$redis->set("username:" . $username,$uid);
header('location:list.php');