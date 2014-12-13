<?php
require_once "parser/parse_handler.php";
class ProcessPost {
	public $guestStatus = false;
	public function __construct() {
		$this->guestStatus = false;
	}
	public function processIndex($post) {
		$post['username'] = htmlspecialchars($post['username']);
		$post['title'] = htmlspecialchars($post['title']);
		$post['email'] = htmlspecialchars($post['email']);
		$post = $this->usernameCheck($post);
		$this->handler = new ParseHandler;
		$post['content'] = $this->handler->getHtml($post['content']);
		$post['content'] = preg_replace('/(?<=\n|^)(\\&gt; )([^\n]+)/', "<span class='quote'>$1$2</span>", $post['content']);
		$post = $this->separateTime($post);
		return($post);
	}
	public function usernameCheck($post) {
		if(empty($post['username'])) {
			$post['username'] = "Unnamed";
			$this->guestStatus = true;
		} else {
			$this->guestStatus = false;
		}
		return($post);
	}
	public function validateBlank() {
		$matches;
		if (preg_match_all('/[\\S]/', $_POST['content'], $matches) <= 0) {
			header("Location: http://{$_SERVER['SERVER_NAME']}/chatbox");
			die();
		}
		if (preg_match_all('/[\\S]/', $_POST['username'], $matches) <= 0) {
			unset($_POST['username']);
			var_dump($_POST['username']);
		}
		if (preg_match_all('/[\\S]/', $_POST['title'], $matches) <= 0) {
			unset($_POST['title']);
		}
	}
	public function separateTime($post) {
		if (!empty($post["time"])) {
			$time = new Datetime($post["time"]);
			$time->modify("+4 hour");
		} else {
			$time = "";
		}
		$post["modTime"] = $time;
		return($post);
	}
}