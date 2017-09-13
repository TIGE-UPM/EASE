<?php
	class Form_Login extends Form_Base{
		public function init()
	    {
	        $username = $this->addElement('text', 'username', array(		
	            'filters'    => array('StringTrim', 'StringToLower'),		            
				'decorators' => $this->paragraphedDecorators,
	            'required'   => true,
	            'label'      => 'Email:',
//				'validators' => array('EmailAddress'),
	        ));

	        $password = $this->addElement('password', 'password', array(
	            'filters'    => array('StringTrim'),
	            'validators' => array(
	                'Alnum',
	                array('StringLength', false, array(6, 20)),
	            ),
				'decorators' => $this->paragraphedDecorators,
	            'required'   => true,
	            'label'      => 'Contraseña:',
	        ));

	        $login = $this->addElement('submit', 'login', array(
				'decorators' => $this->buttonDecorators,
	            'required' => false,
	            'ignore'   => true,
	            'label'    => 'Login',
	        ));
			$this->addDisplayGroup(array('username', 'password', 'login'), "loginBox", array("legend"=>"Acceso"));

	        // We want to display a 'failed authentication' message if necessary;
	        // we'll do that with the form 'description', so we need to add that
	        // decorator.
	        $this->setDecorators(array(
	            'FormElements',
	            array('Description', array('placement' => 'prepend')),	            
				array('Form', array('id'=>'login'))	            
	        ));
	    }
	}
?>