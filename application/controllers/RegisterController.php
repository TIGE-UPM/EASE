<?php
	class RegisterController extends Zend_Controller_Action {
		function gameselectionAction(){
			$this->view->headTitle("Registrar Usuario - Elegir Juego", 'PREPEND');
			$this->view->controllerName='register';
			$games = new Model_DbTable_Games();
			$this->view->games=$games->getAllNonTemplateGames();
		}
		function indexAction(){
			
			$this->view->headTitle("Registrar Usuario", 'PREPEND');
			$this->view->controllerName='register';
			
			$game_id = $_GET['game_id'];
			$authHelper=Zend_Controller_Action_HelperBroker::getStaticHelper('authHelper');
			if (! $authHelper->isLogged()){
				if ($game_id!=null){
					$games = new Model_DbTable_Games();
					$game = $games->getGame($game_id);
					if ($game != null){
						$companiesInGame=$games->getCompaniesInGame($game_id);
						$companies=array();
						foreach ($companiesInGame as $company){
							$companies[$company['id']]=$company['name'];
						}
						$roles=array('Dir. General', 'Dir. Económico-Financiero', 'Dir. Producción', 'Dir. Recursos Humanos', 'Dir. Marketing');
						$options=array('companies'=>$companies, 'roles'=>$roles);
						$form = new Form_Register($options);
						$this->view->form=$form;
						if ($this->getRequest()->isPost()){//¿se ha enviado?
							$formData=$this->getRequest()->getPost();
							if (!$form->isValid($formData)) {//no es válido
								$form->populate($formData);
								$this->view->form = $form;								
							}
							else{//sí es válido
								$companies=new Model_DbTable_Companies();
								$company=$companies->getCompany($formData['company_id']);
								
								if ($company['registration_password']==$formData['registration_password']){//¿concuerda el password con el equipo?
									$users = new Model_DbTable_Users();
									if (! $users->exists(array('email = '.'"'.$formData['email'].'"'))){//¿existe ese email?
										if (! $users->exists(array('company_id = '.$formData['company_id'], 'role = '.$formData['role']))){//¿está asignado ese rol ya?
											if (strlen($formData['pass'])>=6){
												if ($formData['pass']==$formData['repeat_pass']){
													$userData=array('name'=>$formData['name'], 'surname'=>$formData['surname'], 'email'=>$formData['email'], 'company_id'=>$formData['company_id'], 'is_admin'=>0, 'role'=>$formData['role'], 
																	'password'=>md5($formData['pass']));										
													$users->addUser($userData);
													
													$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');												
													$redirector->gotoUrl("login?register=ok");												
												}
												else{//no coinciden el pass y la comprobación
													$this->view->error = "Password y repetición no son iguales";
												}
											}
											else{
												$this->view->error = "La contraseña de usuario debe ser, como mínimo, de 6 caracteres";
											}
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
								else{//el password del equipo no coincide
									$form->populate($formData);				
									$this->view->error = "Password de registro incorrecto";					
								}
							}
						}
					}
					else{// El juego especificado no existe
						$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
						$redirector->direct("error");
					}
				}
				else{// No se ha especificado juego al que registrarse
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->direct("gameselection");
				}
			}
		
			else{// Usuario logeado, no puede registrarse
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
				$redirector->direct("index", "index");			
			}
		}
	
	}
?>