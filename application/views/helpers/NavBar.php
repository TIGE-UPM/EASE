<?php
class Zend_View_Helper_NavBar{
	function NavBar($view, $controllerName){
		$this->view=$view;
		$this->controllerName=$controllerName;
		$authHelper=Zend_Controller_Action_HelperBroker::getStaticHelper('authHelper');
		if ($authHelper->isLogged()){
			if ($authHelper->isAdmin()){
				$tabs=array('index'=>'inicio', 'game'=>'juegos');
			}
			else{
				$tabs=array('index'=>'inicio', 'decision'=>'decisiones', 'outcomes'=>'resultados');
			}
			$this->bar($tabs);
		}	
	}
	function bar($tabs){
				echo '	<style>
					.black_overlay{
						display: none;
						position: absolute;
						top: 0%;
						left: 0%;
						width: 100%;
						height: 100%;
						background-color: black;
						z-index:1001;
						-moz-opacity: 0.8;
						opacity:.80;
						filter: alpha(opacity=80);
					}
					.white_content {
						display: none;
						position: absolute;
						top: 30%;
						left: 33%;
						width: 33%;
						height: 40%;
						padding: 16px;
						border: 16px solid #336699;
						background-color: white;
						z-index:1002;
						overflow: auto;
					}
				</style>
				<div id="light" class="white_content">
					<div id="imgLOAD" style="text-align:center;">
						<b>Cargando...</b>
						</br>
						</br>
					</div>
					<div style="text-align:center;">
						<img src="/images/loading.gif"/>
					</div>
				</div>
				<div id="fade" class="black_overlay"></div>
				<script type="text/javascript">
					window.onload = detectarCarga();
					function detectarCarga(){
						document.getElementById("imgLOAD").style.display="none";
						document.getElementById("light").style.display="none";
                        document.getElementById("fade").style.display="none";	
					}
					function Cargar(){
						document.getElementById("imgLOAD").style.display="block";
                        document.getElementById("light").style.display="block";
                        document.getElementById("fade").style.display="block";						
					}
				</script>';
		echo '<div id="navBar">'."\n"
			 .'  <ul>'."\n";
			 foreach ($tabs as $tab=>$value){
				if ($tab != $this->controllerName){
					if($tab=='outcomes'){
						echo '    <li><a href="'.$this->view->url(array('controller'=>$tab, 
																		'action'=>'index')).'" onClick="Cargar();">'.$value.'</a></li>'."\n";
					}
					/*if($tab=='notifications'){
						echo '<li><a href="'.$this->view->url(array('controller'=>'index','action'=>$tab)).'">'.$value.'</a></li>'."\n";
					}*/
					else {
						echo '    <li><a href="'.$this->view->url(array('controller'=>$tab, 
																		'action'=>'index')).'">'.$value.'</a></li>'."\n";
					}	
				}
				else{
					if($tab=='outcomes'){
						echo '    <li><a id="currentTab" href="'.$this->view->url(array('controller'=>$tab, 
																	'action'=>'index')).'" onClick="Cargar();">'.$value.'</a></li>'."\n";
					}
					/*if($tab=='notifications'){
						echo '<li><a href="'.$this->view->url(array('controller'=>'index','action'=>$tab)).'">'.$value.'</a></li>'."\n";
					}*/
					else {
						echo '    <li><a id="currentTab" href="'.$this->view->url(array('controller'=>$tab, 
																	'action'=>'index')).'">'.$value.'</a></li>'."\n";
					}
				}
			 }
	
		echo  '    </ul>'."\n"
		     .'</div>'."\n";
	}

}
?>