<?php

class UserController extends Zend_Controller_Action{
	public $_controllerTitle= "Usuarios";
	public function preDispatch(){
		$this->view->title = $this->_controllerTitle;
		$this->_helper->authHelper();
		$this->_helper->adminControl();		
    }
	function indexAction(){
		$this->view->headTitle($this->view->title, 'PREPEND');
		$this->view->controllerName='user';
		$this->view->actionName="index";
		
		$users = new Model_DbTable_Users();
		$this->view->users=$users->getAllUsers();
	}
	function deleteAction(){			
		if (isset ($_GET['user_id'])){
			$this->view->headTitle($this->view->title, 'PREPEND');						
			$users = new Model_DbTable_Users();
			$user = $users->getUser($_GET['user_id']);
			$company_id=$user['company_id'];
			$this->view->users=$users->deleteUser($_GET['user_id']);
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$redirector->gotoUrl($url."company/edit?company_id=".$company_id);
		}
	}
}
?>