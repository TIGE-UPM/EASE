<?php
	class Form_Company extends Form_Base{
		public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            array('Form', array('class'=>'general'))
	        ));
	    }
		public function init(){
			$this->addElement('text', 'name', array(
				'decorators' => $this->paragraphedDecorators,
			            'label'       => 'Nombre',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
			$this->addElement('text', 'registration_password', array(
				'decorators' => $this->paragraphedDecorators,
			            'label'       => 'Contraseña de Registro',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
		
			$this->addElement('submit', 'submit', array(
				'decorators' => $this->buttonDecorators,			
				'label' => 'Crear',
				'ignore'=>true,
			));
			$this->addDisplayGroup(array('name', 'registration_password', 'submit'), "loginBox", array("legend"=>"Crear Empresa"));
		}
	}
?>