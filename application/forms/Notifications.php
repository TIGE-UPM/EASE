<?php
	class Form_Notifications extends Form_Base{				
		public function loadDefaultDecorators()
		{
			$this->setDecorators(array(
					'FormElements',
					array('Form', array('class'=>'general'))
			));
		}			
		public function createForm($labels, $company_id, $formData){			
			if ($formData[0]['team_id']=='0'){							
				$this->addElement('textarea', 'textarea', array('label'=>'General', 'cols'=>'10', 'rows'=>'2', 'value'=>$formData[0]['data']));
			}else {
				$this->addElement('textarea', 'textarea', array('label'=>'General', 'cols'=>'10', 'rows'=>'2', 'value'=>''));
			}
			$value = array();
			$id = array();
			for ($i=0; $i<count($labels); $i++){
				foreach ($formData as $form){
					$value[]=$form['data'];
					$id[]=$form['team_id'];
				}				
				$position = array_search($company_id[$i], $id);						
				if ($position>=0 && $position==true)
					$this->addElement(
							'textarea', 'textarea'.$i, array('label'=>$labels[$i], 'cols'=>'10', 'rows'=>'2', 'value'=>$value[$position]));
				else
					$this->addElement(
							'textarea', 'textarea'.$i, array('label'=>$labels[$i], 'cols'=>'10', 'rows'=>'2', 'value'=>''));							
			}									
			$this->addElement('submit', 'send', array(
					'decorators' => $this->buttonDecorators,
					'required' => false,
					'ignore'   => true,
					'label'    => 'Enviar',
			));
			$text= array();
			$text[]='textarea';
			for ($i=0;$i<count($labels);$i++){
				$text[]='textarea'.$i;
			}
			$text[]='send';			
			$this->addDisplayGroup($text, "notificationBox");
			
		}
		
	}

?>