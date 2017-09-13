<?php
	class Form_Register extends Form_Base{
		public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            array('Form', array('class'=>'general'))
	        ));
	    }
		public function __construct($options=null){
			parent::__construct();
			$this->getElement('company_id')->setMultiOptions($options['companies']);
			$this->getElement('role')->setMultiOptions($options['roles']);
			
		}
		public function init(){
			
			$this->addElement('select', 'company_id', array(
				'decorators' => $this->paragraphedDecorators,
	            'label'       => 'Equipo',
				'required' => true,
	        ));
	
			$this->addElement('password', 'registration_password', array(				
				'decorators' => $this->paragraphedDecorators,				
	            'label'       => 'Contraseña de registro',
				'required' => true,	
	        ));
			$this->addElement('select', 'role', array(
				'decorators' => $this->paragraphedDecorators,				
            	'label'       => 'Cargo',
        	));
			$this->addElement('text', 'name', array(
				'decorators' => $this->paragraphedDecorators,
			            'label'       => 'Nombre',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
			$this->addElement('text', 'surname', array(
				'decorators' => $this->paragraphedDecorators,
			            'label'       => 'Apellidos',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
			$this->addElement('text', 'email', array(
				'decorators' => $this->paragraphedDecorators,
			            'label'       => 'Email',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
			$this->addElement('password', 'pass', array(
				'decorators' => $this->paragraphedDecorators,				
			            'label'       => 'Contraseña',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
			$this->addElement('password', 'repeat_pass', array(
				'decorators' => $this->paragraphedDecorators,				
			            'label'       => 'Repetir Contraseña',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
	
			$this->addElement('submit', 'submit', array(
				'decorators' => $this->buttonDecorators,
				
				'label' => 'Registrarse',
			));
			$this->addDisplayGroup(array('company_id', 'registration_password', 'role', 'name', 'surname', 'email', 'pass', 'repeat_pass', 'submit'), "loginBox", array("legend"=>"Registrar usuario"));
		}
	}
?>

