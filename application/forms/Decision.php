<?php
	class Form_Decision extends Zend_Form{
		public function __construct($options=null){
			parent::__construct($options);
			$this->setName('decision');
		}
	}
?>