<?php
class Form_HumanResources extends Form_Base{

	    public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            'Form',
	        		));
	    }
		public function init(){
			// Politica Salarial
			$this->addElement('hidden', 'humanResources_cuartil_header', array(
			    'decorators' => $this->openSectionDecorators,
			    'label'       => 'Política Salarial ',
				));
		    $this->addElement('select', 'humanResources_cuartil', array(
	            'decorators' => $this->elementDecorators,
				'multiOptions' =>array(1=>'Primer Cuartil',2=>'Segundo Cuartil', 3=>'Tercer Cuartil')
	        	));
	        $this->addElement('hidden', 'humanResources_cuartil_close', array(
		            'decorators' => $this->closeSectionDecorators
		    	));
		
	
			// Cualificacion
			$this->addElement('hidden', 'humanResources_formation_header', array(
	            'decorators' => $this->openSectionDecorators,
	            'label'       => 'Cualificación del Staff ',
				));

			$this->addElement('select', 'humanResources_formation', array(
		        'decorators' => $this->elementDecorators,
				'multiOptions' =>array(1=>'Experto',2=>'Avanzado', 3=>'Basico')
		     	));
		
		     $this->addElement('hidden', 'humanResources_formation_close', array(
		            'decorators' => $this->closeSectionDecorators
		    	));
		
		}
	
	    
	
	}
?>