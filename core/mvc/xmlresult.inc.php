<?php
class CSX_Mvc_XmlResult extends CSX_Mvc_ActionResult {
	protected $xml;

	public function __construct($xml) {
		$this->xml = $xml;
	}
	
	public function getResult() {
		CSX_Server::getResponse()->setContentType('text/xml');
		return '<' . '?xml version="1.0" encoding="utf-8"?' . ">\n" . $this->xml;
	}
}