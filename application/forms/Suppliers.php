<?php
	class Form_Suppliers extends Form_Base{

	    public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            'Form',
	        ));
	    }
		public function init(){
			// Número de Proveedores
			$this->addElement('hidden', 'suppliers_number_header', array(
			            'decorators' => $this->openSectionDecorators,
			            'label'       => 'Número de Proveedores ',
			));
		    $this->addElement('select', 'suppliers_number', array(
	            'decorators' => $this->elementDecorators,
				'multiOptions' =>array(1=>'1',2=>'2', 3=>'3', 4=>'4', 5=>'5')
	        ));
	
			// Plazos de pago
			$this->addElement('hidden', 'suppliers_payterms_header', array(
	            'decorators' => $this->openSectionDecorators,
	            'label'       => 'Plazos de Pago ',
			));

			for ($channel=1; $channel<=3; $channel++){
				// Nueva Fila
				$this->addElement('hidden', 'suppliers_payterms_open_row'.($channel), array(
			            'decorators' => $this->openRowDecorators,
				));
				
				$this->addElement('hidden', 'suppliers_payterms_channel_'.$channel.'_header', array(
		            'decorators' => $this->headerInLineDecorators,
		            'label'      => 'Canal '.$channel,
		        ));
			

				$this->addElement('select', 'suppliers_payterms_channel_'.$channel, array(
		            'decorators' => $this->oneCellInLineDecorators,
					'multiOptions' =>array(0=>'Inmediato',30=>'aplazado 30 días', 60=>'aplazado 60 días', 90=>'aplazado 90 días')
		        ));

				// Cierra Fila
				$this->addElement('hidden', 'suppliers_payterms_close_row'.($channel), array(
		            'decorators' => $this->closeRowDecorators
		    	));
			}// for channel

			$this->addElement('hidden', 'suppliers_payterms_close', array(
		            'decorators' => $this->closeSectionDecorators
		    ));					    
		}
	
	    
	
	}
?>