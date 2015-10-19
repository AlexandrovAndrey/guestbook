<?php
class Guestbook {
	private $messages;
	private $file;
	
	public function __construct($file){
		try{
			$this->file = $file;
			$xml_file = @file_get_contents($file);
			if (!$xml_file) throw new Exception('Не найден XML файл данных или некорректный его формат.', 1);
			$messages = new SimpleXMLElement(file_get_contents($file));
			$messages_arranged = array();
			$messages = (array) $messages;
			$messages = $messages['message'];
			foreach ($messages as $msg){
				$msg = (array) $msg;
				$parent_id = (string) $msg['parent_id'];
				if ($parent_id === "0") {
					// 1 уровень - сам отзыв
					$messages_arranged[ $msg['id'] ] = $msg;
				} else {
					$parents = explode('_',  $msg['parent_id']);
					if (count($parents) == 1){
						// 2 уровень - ответ на отзыв
						if (!isset($messages_arranged[ $msg['parent_id'] ]['childrens'])){
							$messages_arranged[ $msg['parent_id'] ]['childrens'] = array();
						}
						$messages_arranged[ $msg['parent_id'] ]['childrens'][$msg['id']] = $msg;
					} else {
						// 3 уровень
						if (!isset($messages_arranged[ $parents[1] ]['childrens'][ $parents[0]]['childrens'])) {
							$messages_arranged[ $parents[1] ]['childrens'][ $parents[0]]['childrens'] = array();
						}
						$messages_arranged[ $parents[1] ]['childrens'][ $parents[0]]['childrens'][ $msg['id'] ] = $msg;
					}
				}
			}

			$messages_result = array();
			foreach ($messages_arranged as $msg0){
				$msg0['level'] = 0;
				$messages_result[] = $msg0;
				if (!isset($msg0['childrens'])) continue;
				foreach ($msg0['childrens'] as $msg1){
					$msg1['level'] = 1;
					$messages_result[] = $msg1;
					if (!isset($msg1['childrens'])) continue;
					foreach ($msg1['childrens'] as $msg2){
						$msg2['level'] = 2;
						$messages_result[] = $msg2;
					}
				}
			}
			$this->messages = $messages_result;
		} catch (Exception $e){
			die($e->getMessage());
		}
	}

	public function getMessages(){
		return $this->messages;
	}

	public function createMessage($msg){
		try{
			$messages = new SimpleXMLElement(file_get_contents($this->file));
			$parent_node = $this->findById($msg['parent_id']);
			if (!$parent_node){
				$parent_id = 0;
			} else {
				if ($parent_node['parent_id'] != '0'){
					$parent_id = $msg['parent_id'].'_'.$parent_node['parent_id'];
				} else {
					$parent_id = $msg['parent_id'];
				}
			}

			$key = $this->getKey();
			if (!$this->canAddMessage($key)) throw new Exception('Извините, вы не можете добавлять сообщения чаще 1 раза  в 10 секунд.');

			$local_msg = $messages->addChild('message');
			$local_msg->addChild('username', $msg['username']);
			$local_msg->addChild('msg_text', $msg['msg_text']);
			$local_msg->addChild('msg_date', $msg['msg_date']);
			$local_msg->addChild('id', $msg['id']);
			$local_msg->addChild('parent_id', $parent_id);
			$local_msg->addChild('key', $key);
			$messages->asXml($this->file);
			return (array)$local_msg;
		} catch (Exception $e){
			die($e->getMessage());
		}
	}

	public function updateMessage($msg){
		try{
			$messages = new SimpleXMLElement(file_get_contents($this->file));
			$i = 0;
			// var_dump_($messages->message[1]);
			foreach ($messages->message as $key => $msg_local){
				$loop_msg_id = (string) $msg_local->id;
				if ($loop_msg_id == $msg['id']){
					// var_dump_($key);
					$messages->message[$i]->msg_text = $msg['msg_text'];
					// var_dump_($messages->message[$i]);
					break;
				}
				$i++;
			}
			$messages->asXml($this->file);
			return true;
		} catch (Exception $e){
			die($e->getMessage());
		}
	}

	private function findById($id){
		foreach ($this->messages as $msg){
			if ($msg['id'] == $id){
				return $msg;
			}
		}
		return false;
	}

	private function getKey(){
		if (isset($_COOKIE['guestbook_key']))
			return $_COOKIE['guestbook_key'];
		else {
			$key = uniqid('', true);
			$_COOKIE['guestbook_key'] = $key;
			setcookie('guestbook_key', $key, time()+3600*24*30, '/');
			return $key;
		}
	}

	private function canAddMessage($key){
		$messages = array_reverse($this->messages);
		foreach ($messages as $msg){
			$msg_date = new DateTime($msg['msg_date']);
			$now = new DateTime('now');
			$interval = 1*$now->getTimestamp() - 1*$msg_date->getTimestamp();
			// var_dump_($interval);
			if ($interval > 10){
				break;
			} else {
				if ($msg['key'] == $key){
					return false;
				}
			}
		}
		return true;
	}
}