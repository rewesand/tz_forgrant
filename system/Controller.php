<?php 

abstract class Controller {

	public $request;

	function __construct(Request $request)
	{
		$this->request = $request;
		return $this;
	}

	abstract function before();
	abstract function after();

}
