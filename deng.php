<?php
require('./redis.php');
$username=isset($_POST['username'])?$_POST['username']:false;
$pass = isset($_POST['password'])?$_POST['password']:false;
$id = $redis->get("username:" . $username);
if(!empty($id)){
	$password = $redis->hget("user:" . $id,"password");
	if(md5($pass) == $password){
		$auth = md5(time() . $username . rand());
		$redis->set("auth:" . $auth,$id);
		setcookie("auth",$auth,time()+86400);
//		echo $_COOKIE['auth'];
		header("location:list.php");
	}
}
?>