<?php
	class ValidateController extends Zend_Controller_Action {
		public $_controllerTitle= "Juegos - ";
		public function preDispatch(){
			$this->view->title = $this->_controllerTitle;
			$this->_helper->authHelper();
			$this->_helper->adminControl();			
	    }
		function editAction(){
			$this->view->title .= " Editar - Editar Empresa.";
			$this->view->headTitle($this->view->title, 'PREPEND');			
			$companies = new Model_DbTable_Companies();
			$this->view->roles=array('Dir. General', 'Dir. Económico-Financiero', 'Dir. Producción', 'Dir. Recursos Humanos', 'Dir. Marketing');
			if (isset($_POST['company'])){
				$data=$_POST['company'];
				$companies->update(array('name'=>$data['name'], 'registration_password'=>$data['register_pass']), 'id='.$_GET['company_id']);
			}
			$this->view->company = $companies->getCompany($_GET['company_id']);			
			$this->view->users = $companies->getUsersInCompany($_GET['company_id']);
		}
		function confirmdeleteAction(){
			$this->view->title .= " Editar - Borrar Empresa";
			$this->view->headTitle($this->view->title, 'PREPEND');	
			$companies = new Model_DbTable_Companies();				
			$this->view->company = $companies->getCompany($_GET['company_id']);	
		}
		function deleteAction(){
			$this->view->title .= " Editar - Borrar Empresa";
			$this->view->headTitle($this->view->title, 'PREPEND');		
			$companies = new Model_DbTable_Companies();	
			$company = $companies->getCompany($_GET['company_id']);	
			$game_id=$company['game_id'];
			$companies->delete('id='.$_GET['company_id']);
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$redirector->gotoUrl($url."game/edit?game_id=".$game_id);
			
		}
		function adduserAction(){
			$this->view->title .= " Editar - Empresa - Añadir Usuario.";			
			$this->view->headTitle($this->view->title, 'PREPEND');
			$roles=array('Dir. General', 'Dir. Económico-Financiero', 'Dir. Producción', 'Dir. Recursos Humanos', 'Dir. Marketing');
			$options=array('roles'=>$roles);
			$form = new Form_User($options);
			$this->view->form=$form;
			$companies=new Model_DbTable_Companies();
			$company=$companies->getCompany($_GET['company_id']);
			$this->view->company=$company;
			if ($this->getRequest()->isPost()){//¿se ha enviado?
				$formData=$this->getRequest()->getPost();
				if (!$form->isValid($formData)) {//no es válido
					$form->populate($formData);
					$this->view->form = $form;								
				}
				else{//sí es válido
					$companies=new Model_DbTable_Companies();
					$company=$companies->getCompany($_GET['company_id']);
					
					$users = new Model_DbTable_Users();
					
					if (! $users->exists(array('email = '.'"'.$formData['email'].'"'))){//¿existe ese email?
						if (! $users->exists(array('company_id = '.$_GET['company_id'], 'role = '.$formData['role']))){//¿está asignado ese rol ya?
							$userData=array('name'=>$formData['name'], 'surname'=>$formData['surname'], 'email'=>$formData['email'], 'company_id'=>$_GET['company_id'], 'is_admin'=>0, 'role'=>$formData['role'], 
											'password'=>md5($formData['password']));										
							$users->addUser($userData);
							$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
							$redirector->gotoUrl($url."company/edit?company_id=".$_GET['company_id']);
						}
						else{//ya existe ese rol
							$form->populate($formData);
							$this->view->error = "Ya existe ese rol";
						}
					}
					else{//ya existe ese email
						$form->populate($formData);				
						$this->view->error = "email ya registrado";										
					}				
				}
			}						
		}
		function edituserAction(){
			$this->view->title = " Editar - Editar Usuario";
			$this->view->headTitle($this->view->title, 'PREPEND');
			$roles=array('Dir. General', 'Dir. Económico-Financiero', 'Dir. Producción', 'Dir. Recursos Humanos', 'Dir. Marketing');
			$options=array('roles'=>$roles);
			$form = new Form_EditUser($options);
			$this->view->form=$form;
			
			$users = new Model_DbTable_Users();
			$user=$users->getUser($_GET['user_id']);
			
			$formData=$user;
			$form->populate($formData);
			$companies=new Model_DbTable_Companies();
			$company=$companies->getCompany($_GET['company_id']);
			$this->view->company=$company;
			if ($this->getRequest()->isPost()){//¿se ha enviado?
				$formData=$this->getRequest()->getPost();
				if (!$form->isValid($formData)) {//no es válido
					$form->populate($formData);
					$this->view->form = $form;								
				}
				else{//sí es válido		
					if (! ($users->exists(array('company_id = '.$_GET['company_id'], 'role = '.$formData['role'])) && $user['role']!=$formData['role'])){//¿está asignado ese rol ya a otro?
								
						$users->update(array('name'=>$formData['name'], 'surname'=>$formData['surname'], 'email'=>$formData['email'], 'company_id'=>$_GET['company_id'], 'is_admin'=>0, 'role'=>$formData['role'], 'password'=>md5($formData['password'])), 'id='.$_GET['user_id']);
						$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
						$redirector->gotoUrl($url."company/edit?company_id=".$_GET['company_id']);
					}
					else{//ya existe ese rol
						$form->populate($formData);
						$this->view->error = "Ya existe ese rol";
					}
													
				}
			}						
		}
		function indexAction(){
			$auth=Zend_Controller_Action_HelperBroker::getStaticHelper('authHelper');
			if ($auth->isAdmin()){
				$this->view->headTitle($this->view->title, 'PREPEND');

				$companies = new Model_DbTable_Companies();
				$games=new Model_DbTable_Games();
				$this->view->games=$games;
				$this->view->companies=$companies->getAllCompanies();	
			}
			else{
				$this->view->headTitle($this->view->title, 'PREPEND');
			}
		}
	}
	
?>