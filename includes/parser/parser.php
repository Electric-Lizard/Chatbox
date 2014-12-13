<?php
require_once "tokenizer.php";
require_once "nodes.php";
class Parser {
	public function __construct() {
		$this->validTags = array("b", "i", "u", "s", "left", "center", "right", "font", "size", "color", "url", "img", "spoiler");
		$this->tagsWithAttributes = array("font", "size", "color");
		$this->tagsWithoutAttributes = array("b", "i", "u", "s", "left", "center", "right");
		$this->tokenizer = new Tokenizer;
	}
	public function getParsedThree($rawText) {
		$this->tokenizer->tokenize($rawText);
		$three = new Root;
		$results = $this->parse();
		$three->content = $results[0];
		return $three;
	}
	protected function parse($parents = array()) {
		$parsedNodes = array(); // root children
		while ($token = $this->tokenizer->getNextToken()) {
			switch ($this->tokenizer->getCurrentTokenType()) {
				case Tokenizer::TOKEN_OPEN_TAG:
				$tag = $this->parseTag($token, $parents);
				$tag? $parsedNodes[] = $tag: $parsedNodes = $this->parsePlainText($parsedNodes, $token);
				break;
				case Tokenizer::TOKEN_CLOSE_TAG:
				$tagName = strtolower(substr($token, 2));
				if ($this->tokenizer->getNextTokenType() == Tokenizer::TOKEN_CLOSE_BRACKET && in_array($tagName, $parents)) {
					if (end($parents) == $tagName) {
						unset($parents[key($parents)]);
						$this->tokenizer->getNextToken(); //skip "]"
						return array($parsedNodes, true);
					} else {
						$this->tokenizer->position -= 1;
						unset($parents[key($parents)]);
						return array($parsedNodes, false);
					}
				} else $parsedNodes = $this->parsePlainText($parsedNodes, $token);
				break;
				case Tokenizer::TOKEN_SMILE:
				$smile = $this->parseSmile($token);
				$smile? $parsedNodes[] = $smile: $parsedNodes = $this->parsePlainText($parsedNodes, $token);
				break;
				case Tokenizer::TOKEN_URL:
				$parsedNodes[] = $this->parseUrl($token);
				break;
				default: $parsedNodes = $this->parsePlainText($parsedNodes, $token);
				break;
			}
		}
		return array($parsedNodes, false);
	}
	protected function parseTag($openTag, $parents) {
		$savedPosition = $this->tokenizer->position;
		$tagName = strtolower(substr($openTag, 1));
		$openTagSrc = $openTag;
		$attrValue = null;
		$parents[] = $tagName;
		if (!in_array($tagName, $this->validTags)) return false;
		if (in_array($tagName, $this->tagsWithAttributes) &&
			$this->tokenizer->getNextTokenType() != Tokenizer::TOKEN_EQUAL_SIGN) return false;
		if (in_array($tagName, $this->tagsWithoutAttributes) &&
			$this->tokenizer->getNextTokenType() != Tokenizer::TOKEN_CLOSE_BRACKET) return false;
		if ($this->tokenizer->getNextTokenType() == Tokenizer::TOKEN_EQUAL_SIGN) {
			$attrValue = $this->tokenizer->getNextToken();
			while ($this->tokenizer->getNextTokenType() != Tokenizer::TOKEN_CLOSE_BRACKET) {
				if ($attrToken = $this->tokenizer->getNextToken()) {
					$attrValue .= $attrToken;
				} else return false;
			}
			$openTagSrc .= $attrValue;
		}
		if ($this->tokenizer->getNextTokenType() == Tokenizer::TOKEN_CLOSE_BRACKET) {
			$openTagSrc .= $this->tokenizer->getNextToken();
		} else {
			$this->tokenizer->position = $savedPosition;
			return false;
		}
		if ($tagName == "img") {
			$node = $this->parseStrictTag($openTagSrc);
			if (!$node) $this->tokenizer->position = $savedPosition;
			return $node;
		}
		$results = $this->parse($parents);
		$tagContent = $results[0];
		$results[1]? $closeTagSrc = "[/".substr($openTag, 1)."]": $closeTagSrc = "";
		return $this->getPairTagNode($tagName, $tagContent, $attrValue, $openTagSrc, $closeTagSrc);
	}
	protected function parseStrictTag($openTagSrc) {
		$tagName = strtolower(substr($openTagSrc, 1, -1));
		$tagContent = array();
		while ($token = $this->tokenizer->getNextToken()) {
			if ($this->tokenizer->getCurrentTokenType() == Tokenizer::TOKEN_CLOSE_TAG &&
				strtolower(substr($token, 2)) == $tagName &&
				$this->tokenizer->getNextTokenType() == Tokenizer::TOKEN_CLOSE_BRACKET) {
				$closeTagSrc = $token.$this->tokenizer->getNextToken();
				return $this->getPairTagNode($tagName, $tagContent, null, $openTagSrc, $closeTagSrc);
			} else {
				$tagContent = $this->parsePlainText($tagContent, $token);
			}
		}
		return false;
	}
	protected function getPairTagNode($tagName, $tagContent, $attrValue, $openTagSrc, $closeTagSrc) {
		$node = new PairTag;
		$node->tagName = $tagName;
		if (isset($attrValue)) $node->attrValue = strtolower(substr($attrValue, 1));
		$position = $this->tokenizer->position;
		$tokens = $this->tokenizer->tokens;
		$this->tokenizer->position = $position;
		$this->tokenizer->tokens = $tokens;
		$node->openTagSrc = $openTagSrc;
		$node->closeTagSrc = $closeTagSrc;
		$node->content = $tagContent;
		return $node;
	}
	protected function parsePlainText($parsedNodes, $token) {
		$token = htmlspecialchars($token);
		if (isset(end($parsedNodes)->type) && end($parsedNodes)->type == Node::NODE_PLAIN_TEXT) {
			end($parsedNodes)->content = end($parsedNodes)->content . $token;
		} else {
			$node = new PlainText;
			$node->content = $token;
			$parsedNodes[] = $node;
		}
		return $parsedNodes;
	}
	protected function parseSmile($token) {
		require "smiles.php";
		$this->mainSmiles = $mainSmiles;
		foreach ($mainSmiles as $smile) {
			if ($smile["code"] == $token) {
				$node = new Smile;
				$node->code = $smile["code"];
				$node->src = $smile["src"];
				if (isset($smile["title"])) $node->title = $smile["title"];
				return $node;
			}
		}
		return false;
	}
	protected function parseUrl($token) {
		if (preg_match('%^(?<![\'"=])https?://www.youtube.com/watch?.*v=([^\\s\\[\\]\\(\\)\\<\\>]+)$%', $token, $matches)) {
			$urlType = "YouTube";
			$id = $matches[1];
		} elseif (preg_match('%^(?<![\'"=])https?://youtu.be/([^\\s\\[\\]\\(\\)\\<\\>]+)$%', $token, $matches)) {
			$urlType = "YouTube";
			$id = $matches[1];
		} else {
			$urlType = "url";
		}
		$node = new Url;
		$node->href = $token;
		$node->urlType = $urlType;
		if (isset($id)) $node->id = preg_replace('/&/', '?', $id, 1);
		return $node;
	}
}