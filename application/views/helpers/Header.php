<?php
class Zend_View_Helper_Header{
	function Header($view, $controllerName='index', $action='index'){
		
		$this->view=$view;
		$this->controllerName=$controllerName;
		$this->action=$action;
		

		
		echo '<div id="pageHeader">'."\n"
			 .'	<div id="topRightLogo">'."\n"
			 .'		<img src="'.$view->baseUrl().'/images/logoUPM.gif'.'">'."\n"
	 		 .'	</div>'."\n"
			 .'	<div id="topLeftLogo">'."\n"
			 .'		<img src="'.$view->baseUrl().'/images/logoTIGE.png'.'">'."\n"
	 		 .'	</div>'."\n"
		     .'</div>'."\n";
		$this->createUserBox();
		$this->createNavBar();
		
	}
	private function createNavBar(){
		switch ($this->controllerName){
			case 'login':
			break;
			case 'register':
			break;
			default:
				$this->view->navBar($this->view, $this->controllerName);
			break;
		}

	}
	private function createSubBar(){
		$this->view->subBar($this->view, $this->controllerName, $this->action);
	}
	private function createUserBox(){
		$this->view->userBox($this->view);
	}	
	
}
?>