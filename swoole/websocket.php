<?php
require_once(__DIR__.'/action.php');
new Websocket();
class Websocket
{
	private $serv;
	private $action;

	public function __construct()
	{
		$this->action = new action();
		$this->serv = new swoole_websocket_server('0.0.0.0',9502);
		$this->serv->on('open',array($this,'onOpen'));
		$this->serv->on('message',array($this,'onMessage'));
		$this->serv->on('close',array($this,'onClose'));
		$this->serv->start();
	}

	public function onOpen($server,$request)
	{
		echo "Welcome {$request->fd} \n";
	}

	public function onMessage($server,$request)
	{
		$data = json_decode($request->data);
		$from_uid = $data->from_uid;
		$to_uid = $data->to_uid;
		$message = $data->message;
		$this->action->unbindFd($from_uid);
		$from_fd = $this->action->bindFd($from_uid,$request->fd);
		if($from_fd) {
			$to_fd = $this->action->getFd($to_uid);
			if($to_fd) {
				$server->push($to_fd,$message);
			} 
		} else {
			$server->push($request->fd,'bind from_fd failed ~~');
		}
	}

	public function onClose($server,$fd)
	{
		$this->action->unbindFd($fd);
		echo "Goodbye {$fd} \n";
	}
}