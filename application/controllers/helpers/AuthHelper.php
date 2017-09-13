<?php
class Zend_Controller_Action_Helper_AuthHelper extends Zend_Controller_Action_Helper_Abstract{
	protected $_user;
	function direct(){
	    if (! $this->isLogged()){
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$redirector->direct('index', 'login');
		}
		else{
			//$this->_user=$this->getLoggedUser();
			$this->getLoggedUser();
			$front = Zend_Controller_Front::getInstance();
			$front->setParam('loggedUserData', $this->getUserData());			
			$this->view=new StdClass;
			$this->view->loggedUser=$this->_user;
			if (! $this->isAdmin()){
				$front->setParam('activeCompany', $this->getUserCompany());
				$front->setParam('activeGame', $this->getActiveGame());
				$front->setParam('activeRound', $this->getActiveRound());
			}
		}    
	}
	function isLogged(){
		return (Zend_Auth::getInstance()->hasIdentity());
	}
	function getLoggedUser(){
		if (!isset($this->_user)){
			$this->_user=Zend_Auth::getInstance()->getIdentity();
		}
	}
		

	function isAdmin(){
		$this->getLoggedUser();
		$userData=$this->getUserData();
		return $userData['is_admin'];

	}
	function getUserData(){
		$this->getLoggedUser();
		$users = new Model_DbTable_Users();
		return $users->getUserByEmail($this->_user);
	}
	function getUserCompany(){
		$user=$this->getUserData();
		$companies = new Model_DbTable_Companies();
		return $companies->fetchRow('id ='.$user['company_id']);
	}
	function getActiveGame(){
		$company=$this->getUserCompany();
		$games = new Model_DbTable_Games();
		return $games->fetchRow('id ='.$company['game_id']);
	}
	function getActiveRound(){
		$game=$this->getActiveGame();
		$rounds=new Model_DbTable_Games_Config_GameRounds();
		return $rounds->getActiveRound($game['id']);
	}
	
}

?>