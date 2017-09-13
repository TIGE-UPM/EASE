		<?php
	class Form_Initiatives extends Form_Base{

	    public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            'Form',
	        ));
	    }
		public function init(){
			// Precios
			$this->addElement('hidden', 'initiatives_number_header', array(
			            'decorators' => $this->openSectionDecorators,
			            'label'       => 'Iniciativas ',
			));		

				// Nueva Fila
				$this->addElement('hidden', 'initiatives_number_open_row1', array(
			            'decorators' => $this->openRowDecorators,
				));
					// Salto 2 celdas
					$this->addElement('hidden', 'initiatives_number_jump1_row1', array(
			            'decorators' => $this->emptyCellDecorators
			    	));
					$this->addElement('hidden', 'initiatives_number_jump2_row1', array(
		            	'decorators' => $this->emptyCellDecorators
		    		));

				// Cierra Fila
				$this->addElement('hidden', 'initiatives_number_close_row1', array(
		            'decorators' => $this->closeRowDecorators
		    	));
			for ($initiativesnumber=1; $initiativesnumber<=3; $initiativesnumber++){
				for ($tipo=1; $tipo<=3; $tipo++){
					// Nueva Fila
					$this->addElement('hidden', 'initiatives_number_open_row'.(3*($initiativesnumber-1)+$tipo), array(
				            'decorators' => $this->openRowDecorators,
					));
						// Si es la central: ponemos etiqueta de initiativesnumbero
						if ($tipo==2){
							$this->addElement('hidden', 'initiatives_number_initiatives_'.$initiativesnumber.'_header', array(
					            'decorators' => $this->headerInLineDecorators,
					            'label'       => 'Numero '.$initiativesnumber,
					        ));
						}
						else{ //sino saltamos la primera columna
							$this->addElement('hidden', 'initiatives_number_jump_tipo_'.$tipo.'_initiatives_'.$initiativesnumber, array(
					            'decorators' => $this->emptyCellDecorators
					    	));	
						}

					$this->addElement('hidden', 'initiatives_number_initiatives_'.$initiativesnumber.'_tipo_'.$tipo.'_header', array(
			            'decorators' => $this->headerInLineDecorators,
			            'label'      => 'Tipo '.$tipo,
			        ));
					
					$this->addElement('select', 'initiativesnumber'.$initiativesnumber.'_tipo.'.$tipo.'_number', array(
				            'decorators' => $this->oneCellInLineDecorators, 'attribs' => array('class' => 'small'),
							'multiOptions' =>array(0=>'Si',1=>'No')
				        ));
			
					// Cierra Fila
					$this->addElement('hidden', 'initiatives_number_close_row'.($initiativesnumber+$tipo), array(
			            'decorators' => $this->closeRowDecorators
			    	));
				}// for tipo
			}// for initiativesnumber
			$this->addElement('hidden', 'initiatives_number_close', array(
		            'decorators' => $this->closeSectionDecorators
		    ));
		    
		    		    
		    
		}
	
	    
	
	}
?>