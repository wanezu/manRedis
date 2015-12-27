<?php
require("./redis.php");
$uid = $_POST['uid'];
$username = $_POST['username'];
$age = $_POST['age'];
$redis->hmset("user:" . $uid,array("username"=>$username,"age"=>$age));
header("location:list.php");