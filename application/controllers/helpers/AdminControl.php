<?php
class Zend_Controller_Action_Helper_AdminControl extends Zend_Controller_Action_Helper_Abstract{
	protected $_user;
	function direct(){
		$front = Zend_Controller_Front::getInstance();
		$userData=$front->getParam('loggedUserData');			 
		if (! (isset($userData) && $userData['is_admin']==1)){
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$redirector->gotoUrl($url."error/forbidden");
		}
	}	
}

?>