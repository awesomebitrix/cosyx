<?php
abstract class CSX_Mvc_View {
	protected $view;
	protected $params;
	
	public function __construct($view, $params = array()) {
		$this->view = $view;
		$this->params = $params;
	}
	
	public abstract function fetch();
}