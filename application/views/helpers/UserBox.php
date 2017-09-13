<?php
class Zend_View_Helper_UserBox{
	function UserBox($view){
		$this->view=$view;
		$authHelper=Zend_Controller_Action_HelperBroker::getStaticHelper('authHelper');
		if ($authHelper->isLogged()){
			$user_data = $authHelper->getUserData();
            echo '<div id="userBox">'."\n"
				.' Bienvenido/a ' . $user_data['email']
				.' <a href="'.$this->view->url(array('controller'=>'login', 'action'=>'logout')).'"> [salir] </a>'."\n"
				.'</div>'."\n";
		}	
		
	}
}
?>