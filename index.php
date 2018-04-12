<?php
echo "start";
echo "adad";
session_start();
if(!$_SESSION['nickname'] && !$_SESSION['uid']){
	echo '<script>window.location.href="login.html";</script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-touch-fullscreen" content="yes" />
	<meta name="format-detection" content="telephone=no"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<META HTTP-EQUIV="pragma" CONTENT="no-cache"> 
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate"> 
<META HTTP-EQUIV="expires" CONTENT="0">
	<meta name="format-detection" content="telephone=no" />
	<meta name="msapplication-tap-highlight" content="no" />
	<meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
	<title>webqq----swoole</title>
	<script type="text/javascript" src="js/jquery.js"></script>
	<style type="text/css">
	html,body{margin:0;padding: 0;background-color: #eee;background-image: url("./images/1.jpg") }
	.userlists{width:280px;height: 620px;border:1px #222 solid;box-shadow:3px 3px 10px #222;border-radius:5px;margin:100px 0px 0px 100px;background-color: #fff}
	.userlists-title{background-color: #222;color:#fff;height: 50px;padding-top:10px;text-align: center;border-radius: 5px 5px 0px 0px;position: relative;}
	.lists_left{height: 500px;overflow-y:scroll;}
	.lists_left::-webkit-scrollbar {display:none}
	.lists_left ul,li{margin:0px;padding:0px;list-style: none}
	.lists_left li{border-bottom:  1px #666 solid;height: 35px;line-height: 35px;}
	.lists_left li a{text-decoration: none;color:#000;height: 100%;display: block;padding-left: 10px}
	.tools{border-radius: 0px 0px 5px 5px;}
	.h10{height: 10px;}
	.h30{height: 500px}
	.circular{height: 30px;width: 30px;border-radius: 30px;background-color: #fff;margin:0 auto;}
	.find{height: 40px;line-height:40px;text-align:center;background-color: #ccc}
	.find input{outline: none}
	.dialogue{width: 600px;height: 600px;background-color: #fff;position: absolute;top:100px;left: 450px;border-radius: 5px；border:1px #222 solid;box-shadow:3px 5px 5px #222;border-radius:5px;display: none;}
	.close{border:1px #fff solid;border-radius:5px;display: inline-block;width: 50px;height: 25px;line-height: 25px;position: absolute;right: 15px;top:12px;}
	.send{height: 50px;line-height: 50px;background-color: #222;border-radius: 0px 0px 5px 5px;text-align: center; }
	.send input[name='content']{height: 30px;width:480px;padding:0px 5px;outline: none}
	.send input[name='sendBtn']{height: 34px;width:80px;display: inline-block;}
	.chat-line{width:360px;border-radius: 10px;margin:10px;padding: 10px;word-wrap:break-word}
	.from{border:1px red solid;float: right;}
	.to{border:1px green solid;float: left;}
	.all{position: absolute;top:0;right: 100px}
	.scroll_box{position: relative;overflow-y:scroll;height: 500px};
	.scroll_box::-webkit-scrollbar {display:none}
	.lists  {position: absolute;left: 0;top: 0;}

	</style>
</head>
<body>
	<div class="userlists">
		<div class="userlists-title"><?php echo $_SESSION['nickname'];?><br>好友列表</div>
		<div class="lists_left">
			<ul id="friend_lists">
			</ul>
		</div>
		<div class="userlists-title tools"><div class="h10"></div><div class="circular"></div></div>
	</div>

	<div class="dialogue">
		<div class="userlists-title">正在与 <t id="uname">.....</t> 聊天 <span class="close">关闭</span></div>
		<div class="scroll_box">
			<div class="lists " id="chat-box">
			</div>
		</div>
		<div class="send">
			<input type="hidden" name="to_uid" >
			<input type="text" name="content" placeholder="发送内容">
			<input type="button" name="sendBtn" id="sendMessage" value="发送">
		</div>
	</div>



	<script type="text/javascript">
		$(function(){
			$.post("./swoole/action.php",{from_uid:<?php echo $_SESSION['uid'];?>,typ:'friendLists'},function(res){
				var r = eval("(" + res + ")");
				if(r.data) {
					var h = "";
					for(var i = 0; i< r.data.length; i++) {
						var status = r.data[i].status =="offline" ? "离线" : "在线" ; 
						h += '<li><a data-id="' + r.data[i].id + '" data-nickname="' + r.data[i].nickname + '" class="friend" href="javascript:;">' +  r.data[i].nickname + " &nbsp;( " + status +' ) </a></li>';
					}
					$("#friend_lists").html(h);
				}
			});


			$("#friend_lists").on("click",".friend",function(){
				var to_uid = $(this).attr("data-id");
				var nickname = $(this).attr("data-nickname");
				$("#uname").html(nickname);
				$("input[name='to_uid']").val(to_uid);
				$.post("./swoole/action.php",{from_uid:<?php echo $_SESSION['uid'];?>,to_uid:to_uid,typ:"loadHistory"},function(res){
					var r = eval("(" + res + ")");
					if(r.data) {
						var h = "";
						for (var i = r.data.length - 1; i >= 0; i--) {
							if(r.data[i].from_uid == <?php echo $_SESSION['uid'];?>) {
								h += '<div class="chat-line from">' + r.data[i].message + '</div>';
							} else {
								h += '<div class="chat-line to">' + r.data[i].message + '</div>';
							}
						}
						$("#chat-box").html(h);
						srcollBox()
					}
				});
				$(".dialogue").show();
				

			});

			$("#sendMessage").on("click",function(){
				if($("input[name='content']").val()) {
					srcollBox()
					sendMessage();

				} else {
					alert("please input your message~");
				}
				
			});
			$(document).keyup(function(evt){
				if(evt.keyCode == 13) {
					if($("input[name='content']").val()) {
						srcollBox()
						sendMessage();
					} else {
						alert("please input your message~");
					}
				}
			});

			$(".close").on("click",function(){
				$(".dialogue").hide();
			});
			function srcollBox(){
				var h = $(".lists").height();
				$(".scroll_box").scrollTop(h,4000)
			}
			srcollBox();

			if(window.WebSocket){
				var ws = new WebSocket("ws://192.168.0.140:9502");
				ws.onopen = function(evt){
					console.log("Connect WebSocket succuess ~~ \n");
				}
				ws.onmessage = function(evt){
					$("#chat-box").append('<div class="chat-line to">' + evt.data + '</div>');
					srcollBox();
					console.log("message on server : " + evt.data + "\n");
				}
				ws.onclose = function(evt){
					console.log("WebSocket closed ~~\n");
				}
				ws.onerror = function(evt){
					console.log("Connect WebSocket failed ~~\n");
				}
				function sendMessage(){
					var params = {
						from_uid : <?php echo $_SESSION['uid'];?>,
						to_uid : $("input[name='to_uid']").val(),
						message : $("input[name='content']").val(),
						typ : "sendMessage"
					};
					var msg = JSON.stringify(params);
					$("#chat-box").append('<div class="chat-line from">' + $("input[name='content']").val() + '</div>');
					srcollBox();
					$.post("./swoole/action.php",params,function(res){
						var r = eval("(" + res + ")");
						if(r.data) {
							ws.send(msg);
						} else {
							alert("send message failed , insert mysql failed~~\n");
						}
					});
					
				}
			} else {
				alert("Your browser does not support WebSocket !");
			}
		});
	</script>
</body>
</html>
