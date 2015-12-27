<a href="index.php">注册</a>
<?php
require("redis.php");
if(!empty($_COOKIE['auth'])){
	$id = $redis->get("auth:" . $_COOKIE['auth']);
	$name = $redis->hget("user:" . $id,"username");
?>
欢迎您,<?php echo $name;?><a href="./logout.php">退出</a>
<?php
}else{
?>
<a href="login.php">登录</a>	
<?php } ?>
<br />
<?php


//用户总数
$count = $redis->lsize("uid");

//页大小
$page_size = 3;

//当前页码
$page_num = (!empty($_GET['page']))?$_GET['page']:1;

//页总数
$page_count = ceil($count/$page_size);

$ids = $redis->lrange("uid",($page_num - 1)*$page_size,(($page_num - 1)*$page_size+$page_size-1));

/*
var_dump($ids);

for($i = 1;$i<=($redis->get("userid"));$i++){
	$data[] = $redis->hgetall("user:" . $i);
*/


foreach($ids as $v){
	$data[] = $redis->hgetall("user:" . $v);
}


//$data = array_filter($data);
?>

<table border=1>
	<tr>
		<th>uid</th>
		<th>用户名</th>
		<th>年龄</th>
		<th>操作</th>
	</tr>
	<?php foreach($data as $v){?>
	<tr>
		<th><?php echo $v['uid'];?></th>
		<th><?php echo $v['username'];?></th>
		<th><?php echo $v['age'];?></th>
		<th><a href="del.php?id=<?php echo $v['uid'];?>">删除</a>&nbsp;&nbsp;<a href="mod.php?id=<?php echo $v['uid'];?>">编辑</a>
		<?php if(!empty($_COOKIE['auth'])&&$id!=$v['uid']){ ?>
			&nbsp;&nbsp;<a href="addfans.php?id=<?php echo $v['uid'];?>&uid=<?php echo $id;?>">关注</a>
			<?php }?></th>
	</tr>
	<?php }?>
	<tr>
		<td colspan="4">
		<a href="?page=<?php echo (($page_num - 1)<=1)?1:($page_num-1) ?>">上一页</a>
		<a href="?page=<?php echo (($page_num + 1)>=$page_count)?$page_count:($page_num+1) ?>">下一页</a>
		<a href="?page=1">首页</a>
		<a href="?page=<?php echo $page_count;?>">尾页</a>
		当前<?php echo $page_num;?>页
		总共<?php echo $page_count;?>页
		总数<?php echo $count;?>用户
		</td>
	</tr>
</table>
<?php if(!empty($_COOKIE['auth'])){?>
<table border=1>
	<caption>我关注了谁</caption>
	<tr>
		<th>uid</th>
		<th>用户名</th>
		<th>年龄</th>
	</tr>
	<?php
		$data = $redis->smembers("user:" . $id . ":following");
foreach($data as $v){
	$row = $redis->hgetall("user:" . $v);
	?>
	<tr>
		<td><?php echo $row['uid'];?></td>
		<td><?php echo $row['username'];?></td>
		<td><?php echo $row['age'];?></td>
	</tr>
	<?php }?>
</table>
<table border=1>
	<caption>我的粉丝</caption>
	<tr>
		<th>uid</th>
		<th>用户名</th>
		<th>年龄</th>
	</tr>
	<?php
		$data = $redis->smembers("user:" . $id . ":followers");
		foreach($data as $v){
			$row = $redis->hgetall("user:" . $v);
	?>
	<tr>
		<td><?php echo $row['uid'];?></td>
		<td><?php echo $row['username'];?></td>
		<td><?php echo $row['age'];?></td>
	</tr>
	<?php }?>
</table>
<?php }?>