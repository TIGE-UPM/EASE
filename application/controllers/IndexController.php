<?php

class IndexController extends Zend_Controller_Action
{
	public $_controllerTitle= "Inicio";
    public function preDispatch(){
		$this->view->title = $this->_controllerTitle;			
		$this->_helper->authHelper();
		$front = Zend_Controller_Front::getInstance();
		$this->game=$front->getParam('activeGame');//carga el juego actual antes de procesar ninguna acción		
		$this->user=$front->getParam('loggedUserData');
    }

	public function indexAction(){		
		$this->view->headTitle($this->view->title, 'PREPEND');
		$this->view->controllerName='index';
		$this->view->actionName="index"; 
		$this->view->news=array();
		$this->view->notifications=array();
		
		$this->view->notifications[]='<ul class="lista_notificaciones">';
		if ($this->user['is_admin']==1){
		}  
		else{
			$notifications = new Model_DbTable_Notification();
			$general_notification=$notifications->getTeamNotification('0');
			$message=$notifications->getTeamNotification($this->user['company_id']);
			$rounds=new Model_DbTable_Games_Config_GameRounds();
			$active_round=$rounds->getActiveRound($this->game['id']);
			if ($active_round != null){		
				$distanceInSeconds = round(abs(strtotime($active_round['closing_date']) - time()));
			    $distanceInMinutes = round($distanceInSeconds / 60);
				if ($distanceInMinutes>60){
					if ($distanceInMinutes<60*24){
						$timeLeft='Quedan '.$this->m2dh($distanceInMinutes);//más de una hora';
					}
					else {
						$timeLeft='Quedan '.$this->m2dh($distanceInMinutes);//más de una hora';
					}
				}
				else{
					if ($distanceInMinutes>1){
						$timeLeft='Quedan '.$timeLeft=$distanceInMinutes.' minutos';
					}
					else{
						$timeLeft='Quedan '.$timeLeft=$distanceInSeconds.' segundos';
					}
				}		
				$this->view->notifications[]='
					<li>Está abierto el turno '.$active_round['round_number'].
					' (hasta el '. date('d/m/Y \a \l\a\s G:i:s', strtotime($active_round['closing_date'])).')<br>
					'.$timeLeft.'</li><br/>';
				$outcomes=new Model_DbTable_Outcomes();
				$latest_outcomes_round_number=$outcomes->getLatestRoundNumber($this->game['id']);
				if (isset ($latest_outcomes_round_number)){
					$this->view->notifications[]='<li>Publicados los resultados de la ronda '.$latest_outcomes_round_number.'</li><br/>';				
				}
				$next_rounds=$rounds->getNextRounds($this->game['id']);
				if (isset ($next_rounds)){
					$this->view->notifications[]='<li>Próximas decisiones:</li><br/>';
					$this->view->notifications[]='<ul>';
					foreach ($next_rounds as $round){						
						$this->view->notifications[]='<li>Decisión ronda '.$round['round_number'].' (del '.date('d/m/Y', strtotime($round['opening_date'])).' al '.date('d/m/Y', strtotime($round['closing_date'])).')</li>';
					}
					$this->view->notifications[]='</ul>';
				}
				
				
				//$this->view->notifications[]='<ul><li style="color:red;">Ronda 6 suspendida</li></ul>';
				/*$this->view->notifications[]='<ul><li style="color:darkred;text-decoration:underline;">Noticias:</li><ul><li>Diversos rumores apuntan a la próxima aparición de nuevos terminales avanzados en el mercado (Reuters).</li></ul></ul>';	*/			
				if ($general_notification!=false && !empty($general_notification['data']))
					$this->view->notifications[]='<li>'.$general_notification[0]['data'].'</li>';
				if ($message!=false && !empty($message[0]['data']))
					$this->view->notifications[]='<li>'.$message[0]['data'].'</li>';
				//$this->view->notifications[]='</ul>';
			}
			else{
				$outcomes=new Model_DbTable_Outcomes();
				$latest_outcomes_round_number=$outcomes->getLatestRoundNumber($this->game['id']);				
				if (isset ($latest_outcomes_round_number)){
					$this->view->notifications[]='<li>Publicados los resultados de la ronda '.$latest_outcomes_round_number.'</li>';				
				}
				$this->view->notifications[]='<li>No hay ningún turno abierto actualmente.</li>';				
				$next_rounds=$rounds->getNextRounds($this->game['id']);
				if (isset ($next_rounds)){
					$this->view->notifications[]='<li>Próximas decisiones:</li>';
					$this->view->notifications[]='<ul>';
					foreach ($next_rounds as $round){						
						$this->view->notifications[]='<li>Decisión ronda '.$round['round_number'].' (del '.date('d/m/Y', strtotime($round['opening_date'])).' al '.date('d/m/Y', strtotime($round['closing_date'])).')</li>';
					}
					$this->view->notifications[]='</ul>';
				}
				if ($general_notification!=false && !empty($general_notification['data']))
					$this->view->notifications[]='<li>'.$general_notification[0]['data'].'</li>';
				if ($message!=false && !empty($message[0]['data']))
					$this->view->notifications[]='<li>'.$message[0]['data'].'</li>';
			}
		}
		$this->view->notifications[]='</ul>';
	}
	
	public function notificationsAction(){
		$this->view->title .= " Editar - Añadir Notificaciones. ";
		$this->view->headTitle($this->view->title, 'PREPEND');
		$this->view->controllerName='index';
		$this->view->actionName="notifications";		
		$games = new Model_DbTable_Games();
		$game_id=$_GET['game_id'];
		$companies=$games->getCompaniesInGame($game_id);			
		$form = new Form_Notifications();
		$labels = array();
		$company_id = array();
		foreach ($companies as $company){
			$labels[]=$company['name'];
			$company_id[]=$company['id'];						
		}
		
		$numTeams = count($labels);		
		/*$form->createForm($labels);
		$this->view->form=$form;*/		
		$notifications = new Model_DbTable_Notification();		
		if ($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();						
			if (!empty ($formData['textarea'])){
				$notification = array('game_id'=>$game_id, 'data'=>$formData['textarea'], 'team_id'=>'0', 'user_id'=>$this->user['name'], 'date'=>date('Y-m-d H:i:s'));
				$notifications->addNotification($notification);					
			}elseif($notifications->existTeam(0)){
				$notifications->delete('0');				
			}						
			for ($i=0;$i<$numTeams;$i++){
				if (empty ($formData['textarea'.$i])){
					$notifications->deleteNotification($company_id[$i]);
				}else{
					$notification = array('game_id'=>$game_id, 'data'=>$formData['textarea'.$i], 'team_id'=>$company_id[$i], 'user_id'=>$this->user['name'], 'date'=>date('Y-m-d H:i:s'));
					$notifications->addNotification($notification);
				}				
			}
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$redirector->gotoUrl($url."game");				
			
		}else {
			$formData=$notifications->getNotifications($game_id);			
			/*$data = array();
			foreach ($formData as $text){
				$data[]=$text['data'];
			}*/
			$form->createForm($labels, $company_id, $formData);
			$this->view->form=$form;
		}
	}
	
	public function m2dh($mins) { 
            // if ($mins < 0) { 
                // $min = Abs($mins); 
            // } else { 
                // $min = $mins; 
            // } 
            // $H = Floor($min / 60); 
            // $M = ($min - ($H * 60)) / 100; 
            // $hours = $H +  $M; 
            // if ($mins < 0) { 
                // $hours = $hours * (-1); 
            // } 
            // $expl = explode(".", $hours); 
            // $H = $expl[0]; 
            // if (empty($expl[1])) { 
                // $expl[1] = "00"; 
            // } 
            // $M = $expl[1]; 
            // if (strlen($M) < 2) { 
                // $M = $M . "0"; 
            // } 
            // $hours = $H." h. ".$M." min."; 
            // return $hours; 
			$d = floor ($mins / 1440);
			$h = floor (($mins - $d * 1440) / 60);
			$m = $mins - ($d * 1440) - ($h * 60);
			if ($d>0) {
				$time = $d." días, ".$h." horas y ".$m." minutos.";
			} else {
				$time = $h." horas y ".$m." minutos.";
			}
			return $time;
    }    

}



