<?php
class Action
{
	private $conn;
	public function __construct()
	{
		require_once (__DIR__.'/config.php');
		$this->conn = mysqli_connect($database['host'],$database['user'],$database['password'],$database['database']) or die ('Connect mysql failed ~~'.mysqli_connect_error());
	}

	//login
	public function login($nickname,$username,$password)
	{
		session_start();
		$sql = " select `id` from `users` where `username` = '{$username}' ";
		if($query = $this->conn->query($sql)) {
			$row = mysqli_fetch_assoc($query);
			$now = date('Y-m-d H:i:s');
			if($row['id']) {
				$sql = " update `users` set `nickname` = '{$nickname}' , `username` = '{$username}' ,`password` = md5('{$password}') , `login_time` = '{$now}' , `login_num` = (`login_num` + 1) where `id` = {$row['id']} ";
			} else {
				$sql = " insert into `users` (`nickname`,`username`,`password`,`login_time`,`login_num`) values ('{$nickname}' , '{$username}' , md5('{$password}') , '{$now}' ,'1')";
			}
			$this->conn->query($sql);
			$user_id = $this->conn->insert_id;
			$_SESSION['uid'] = $row['id'] ? $row['id'] :  $user_id;
			$_SESSION['nickname'] = $nickname;
			return 1;
		} else {
			return 0;
		}
	}

	//add friend
	public function addFriend($from_uid,$to_uid)
	{
		$sql = " select * from `friend` where `from_uid` = '{$from_uid}' and `to_uid` = '{$to_uid}' ";
		if($query = $this->conn->query($sql)) {
			$is_friend = mysqli_fetch_assoc($query);
			if(!$is_friend['to_uid']){
				if($from_uid == $to_uid) {
					return 2;
				} else {
					$sql = " select `nickname` from `users` where `id` = '{$to_uid}' ";
					$query = $this->conn->query($sql);
					$ret = mysqli_fetch_assoc($query);
					$nickname = $ret['nickname'];
					if($nickname){
						$sql = " insert into `friend` (`from_uid`,`to_uid`,`nickname`) values ('{$from_uid}','{$to_uid}','{$nickname}') ";
						$this->conn->query($sql);
						return array('to_uid'=>$to_uid,'nickname'=>$nickname);
					} else {
						return 3;
					}
				}
			} else {
				return 4;
			}
		} else {
			return 0;
		}
	}

	//friend lists
	public function friendLists($from_uid)
	{
		$sql = " select `id`,`nickname` from `users` where `id` != '{$from_uid}' ";
		if($query = $this->conn->query($sql)) {
			$lists = [];
			while ($row = mysqli_fetch_assoc($query)) {
				$sql_1 = " select `fd` from `fd_tmp` where `uid` = '{$row['id']}' ";
				$query_1 = $this->conn->query($sql_1);
				$ret = mysqli_fetch_assoc($query_1);
				$row['status'] = $ret['fd'] ? 'online' : 'offline' ;
				$lists[] = $row;
			}
			return $lists;
		} else {
			return 0;
		}
	}


	//load history message
	public function loadHistory($from_uid,$to_uid)
	{
		$sql = " select `from_uid`,`to_uid`,`message`,`send_time` from `chat` where  ( (`from_uid` = '{$from_uid}' and `to_uid` = '{$to_uid}') or (`to_uid` = '{$from_uid}' and `from_uid` = '{$to_uid}') ) order by `send_time` desc";
		if($query = $this->conn->query($sql)) {
			$message = [];
			while ($row = mysqli_fetch_assoc($query)) {
				$message[] = $row;
			}
			return $message;
		} else {
			return 0;
		}
	}

	//send message
	public function sendMessage($from_uid,$to_uid,$message)
	{
		$time = date('Y-m-d H:i:s');
		$sql = " insert into `chat` (`from_uid`,`to_uid`,`message`,`send_time`) values ('{$from_uid}','{$to_uid}','{$message}','{$time}') ";
		if($query = $this->conn->query($sql)) {
			$last_id = $this->conn->insert_id;
			return $last_id;
		} else {
			return 0;
		}
	}

	//get fd 
	public function getFd($uid)
	{
		$sql = " select `fd` from `fd_tmp` where `uid` = '{$uid}' ";
		if($query = $this->conn->query($sql)) {
			$row = mysqli_fetch_assoc($query);
			return $row['fd'] ? $row['fd'] : 0;
		} else {
			return 0;
		}
	}

	//bind fd 
	public function bindFd($uid,$fd)
	{
		$sql = " insert into `fd_tmp` (`fd`,`uid`) values ('{$fd}','{$uid}') ";
		if($this->conn->query($sql)) {
			return $fd;
		} else {
			return 0;
		}
	}

	//unbind fd 
	public function unbindFd($fd)
	{
		$sql = " delete from `fd_tmp` where `fd` = '{$fd}' ";
		if($this->conn->query($sql)) {
			return 1;
		} else {
			return 0;
		}
	}

	public function __destruct()
	{
		mysqli_close($this->conn);
	}
}

//process ajax request
if($_POST && isset($_POST['typ']))
{
	$action = new Action();
	switch ($_POST['typ']) {
		case 'login':
			$ret = $action->login($_POST['nickname'],$_POST['username'],$_POST['password']);
			break;
		case 'addFriend':
			$ret = $action->addFriend($_POST['from_uid'],$_POST['to_uid']);
			break;
		case 'friendLists':
			$ret = $action->friendLists($_POST['from_uid']);
			break;
		case 'loadHistory':
			$ret = $action->loadHistory($_POST['from_uid'],$_POST['to_uid']);
			break;
		case 'sendMessage':
			$ret = $action->sendMessage($_POST['from_uid'],$_POST['to_uid'],$_POST['message']);
			break;
	}
	echo json_encode(array('data'=>$ret));
}