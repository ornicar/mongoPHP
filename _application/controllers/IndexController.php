<?php

class IndexController extends Mongo_Controller {
	
	public function indexAction() {
		
		$this->auth()->databaseName		= false;
		$this->auth()->collectionName	= false;
		$this->setVar( 'inDatabase', false );	
		$this->view->databases		= $this->layout->databases;
		
	}
	
}