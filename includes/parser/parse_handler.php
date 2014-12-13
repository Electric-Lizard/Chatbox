<?php
require_once "parser.php";
class ParseHandler {
	public function __construct() {
		$this->parser = new Parser;
	}
	public function getHtml($row) {
		$newRow = '';
		$three = $this->parser->getParsedThree($row);
		while ($node = $three->getNode()) {
			$newRow .= $this->getHtmlFromNode($node);
		}
		return $newRow;
	}
	protected function getHtmlFromNode($node) {
		switch ($node->type) {
			case Node::NODE_PLAIN_TEXT:
			$html = $node->content;
			break;
			case Node::NODE_TAG:
			$html = $this->getHtmlFromTag($node);
			break;
			case Node::NODE_SMILE:
			if (isset($node->title)) {
				$html = "<img src=\"{$node->src}\" alt=\"{$node->code}\" title=\"{$node->title}\">";
			} else $html = "<img src=\"{$node->src}\" alt=\"{$node->code}\">";
			break;
			case Node::NODE_URL:
			$html = $this->getHtmlFromUrl($node);
			break;
		}
		return $html;
	}
	protected function getHtmlFromUrl($node) {
		switch ($node->urlType) {
			case 'url':
			$html = "<a target=\"_blank\" href=\"{$node->href}\">{$node->href}</a>";
			break;
			case 'YouTube':
			$html = "<iframe width=\"560\" height=\"315\" src=\"//www.youtube.com/embed/{$node->id}\" frameborder=\"0\" allowfullscreen></iframe>";
			break;
		}
		return $html;
	}
	protected function gethtmlFromTag($node) {
		$content = "";
		foreach ($node->content as $child) {
			$content .= $this->gethtmlFromNode($child);
		}
		switch ($node->tagName) {
			case 'b':
			case 'i':
			case 'u':
			case 's':
			$html = "<{$node->tagName}>$content</{$node->tagName}>";
			break;
			case 'left':
			case 'center':
			case 'right':
			$html = "<div style=\"text-align:{$node->tagName};\">$content</div>";
			break;
			case 'font':
			if (!preg_match('/^[\\w ]+$/', $node->attrValue)) {
				$html = $node->openTagSrc.$content.$node->closeTagSrc;
			} else {
				$font = htmlspecialchars($node->attrValue);
				$html = "<span style=\"font-family:{$font};\">$content</span>";
			}
			break;
			case 'size':
			if (preg_match('/^(\\d{1,2})(pt)?$/i', $node->attrValue, $matches) && $matches[1] <= 72) {
				$html = "<span style=\"font-size:{$node->attrValue};\">$content</span>";
			} else $html = $node->openTagSrc.$content.$node->closeTagSrc;
			break;
			case 'color':
			$html = "<span style=\"color:{$node->attrValue};\">$content</span>";
			break;
			case 'url':
			if (isset($node->attrValue)) {
				if (!preg_match('/^https?:\/\//', $node->attrValue)) {
					$url = "http://".$node->attrValue;
				} else $url = $node->attrValue;
			} else {
				if (!preg_match('/^https?:\/\//', $node->attrValue)) {
					$url = "http://".$content;
				} else $url = $content;
			}
			$html = "<a target=\"_blank\" href=\"$url\">{$content}<a>";
			break;
			case 'img':
			$content = htmlspecialchars($content);
			$html = "<img src=\"{$content}\">";
			break;
			case 'spoiler':
			if (isset($node->attrValue)) {
				$spoilerName = $node->attrValue;
			} else $spoilerName = "Spoiler";
			$html = "<div class=\"spoiler\"><span class=\"spoiler-head\">$spoilerName</span><div class=\"spoiler-content\">{$content}</div></div>";
			break;
		}
		return $html;
	}
}