<?php

abstract class GAS_Controller_Abstract {
	
	public $view	= null;
	public $layout	= null;
	protected static $_registry;
	
	public function __construct() {
	
		static::$_registry = GAS_Registry::getInstance();
		
		$this->_post	= static::$_registry->globals->clean[ 'post' ];
		$this->_get		= static::$_registry->globals->clean[ 'get' ];
	
	}
	
	public function setLayout( GAS_Layout &$layout ) {
		
		$this->layout	= $layout;
		$this->view		=& $layout->view;
		
		return $this;
	
	}
	
	public function layout() {
	
		return $this->layout;
	
	}

	public function sendResponse() {
	
		if( $this->layout()->enabled() ) {
			
			$layout	= $this->layout()->render();
			
			switch( $this->layout()->format() ) {
			
				default:
					case 'http':
						
						echo $layout;
					
						break;
						
				case 'json':
				
					echo json_encode( array(
					
						'status'	=> 'ok',
						'html'		=> $layout 
					
					));
				
					break;
			
			}
		
		} else {
		
			switch( $this->layout()->format() ) {
			
				default:
					case 'http':
						
						echo $this->layout()->getViewContent();
					
						break;
						
				case 'json':
				
					echo json_encode( array(
					
						'status'	=> 'ok',
						'html'		=> $this->layout()->getViewContent() 
					
					));
				
					break;
			
			}
		
		}
		
	}

}