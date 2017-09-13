<?php
	class Form_NewGame extends Form_Base{
		public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            'Form',
	        ));
	    }
		public function init(){
			$this->addElement('text', 'newGame[name]', array(
			            'label'       => 'Nombre',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
			$this->addElement('textarea', 'newGame[description]', array(
			            'label'       => 'Descripción',
						'filters' => array('StripTags', 'StringTrim'),
			));
		
			$this->addElement('select', 'newGame[template]', array(
	            'label'       => 'Configuración del juego',
				'multiOptions' =>array(0=>'Nueva', 1=>'Desde plantilla')
	        ));
	
			$this->addElement('submit', 'submit', array(
				     	'label' => 'Crear',
			));
			
		}
	}
?>

