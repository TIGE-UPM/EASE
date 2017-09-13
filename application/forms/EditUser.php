<?php
	class Form_EditUser extends Form_Base{
		public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            array('Form', array('class'=>'general'))
	        ));
	    }
		public function __construct($options=null){
			parent::__construct();
			$this->getElement('role')->setMultiOptions($options['roles']);
		}
		public function init(){
			
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
			$this->addElement('password', 'password', array(
				'decorators' => $this->paragraphedDecorators,				
			            'label'       => 'Contraseña',
						'required' => true,
						'filters' => array('StripTags', 'StringTrim'),
						'validator' => 'NotEmpty',
			));
			$this->addElement('submit', 'submit', array(
				'decorators' => $this->buttonDecorators,
				
				'label' => 'Guardar Cambios',
			));
			$this->addDisplayGroup(array('role', 'name', 'surname', 'email', 'password', 'submit'), "userBox", array("legend"=>"Editar usuario"));
		}
	}
?>
