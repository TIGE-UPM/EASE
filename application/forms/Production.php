<?php
	class Form_Production extends Form_Base{

	    public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            'Form',
	        ));
	    }
		public function init(){
			$this->addElement('hidden', 'production_region_header', array(
			            'decorators' => $this->openSectionDecorators,
			            'label'       => 'Región de Fabricación ',
			));
		    $this->addElement('select', 'production_region', array(
	            'decorators' => $this->elementDecorators,
				'multiOptions' =>array(1=>'Britania',2=>'Galia', 3=>'Hispania')
	        ));
		    $this->addElement('hidden', 'production_region_close', array(
		            'decorators' => $this->closeSectionDecorators
		    ));
			$this->addElement('hidden', 'production_quality_header', array(
		            'decorators' => $this->openSectionDecorators,
		            'label'       => 'Calidad ',
			));
			$this->addElement('hidden', 'production_quality_open_row', array(
		            'decorators' => $this->openRowDecorators,
			));
			for ($product=1; $product<=3; $product++){
				$this->addElement('select', 'product'.$product.'_quality', array(
		            'decorators' => $this->elementInLineDecorators,
		            'label'       => 'Product '.$product,
					'multiOptions' =>array(1=>'1',2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8', 9=>'9', 10=>'10')
		        ));
	    	}
			$this->addElement('hidden', 'production_quality_close_row', array(
	            'decorators' => $this->closeRowDecorators
	    	));
		    $this->addElement('hidden', 'production_quality_close', array(
	            'decorators' => $this->closeSectionDecorators
	    	));

			// Unidades a Producir
			$this->addElement('hidden', 'production_units_header', array(
	            'decorators' => $this->openSectionDecorators,
	            'label'       => 'Unidades a producir ',
			));
				// Nueva Fila
				$this->addElement('hidden', 'production_units_open_row1', array(
			            'decorators' => $this->openRowDecorators,
				));
					// Salto 2 celdas
					$this->addElement('hidden', 'production_quality_jump1_row1', array(
			            'decorators' => $this->emptyCellDecorators
			    	));
					$this->addElement('hidden', 'production_quality_jump2_row1', array(
		            	'decorators' => $this->emptyCellDecorators
		    		));
					// Cabeceras Regiones
					for ($region=1; $region<=3; $region++){
						$this->addElement('hidden', 'product_units_region_'.$region.'_header', array(
				            'decorators' => $this->headerInLineDecorators,
				            'label'       => 'Región '.$region,
				        ));
					}
				// Cierra Fila
				$this->addElement('hidden', 'production_units_close_row1', array(
		            'decorators' => $this->closeRowDecorators
		    	));
			for ($product=1; $product<=3; $product++){
				for ($channel=1; $channel<=3; $channel++){
					// Nueva Fila
					$this->addElement('hidden', 'production_units_open_row'.(3*($product-1)+$channel), array(
				            'decorators' => $this->openRowDecorators,
					));
						// Si es la central: ponemos etiqueta de producto
						if ($channel==2){
							$this->addElement('hidden', 'product_units_product_'.$product.'_header', array(
					            'decorators' => $this->headerInLineDecorators,
					            'label'       => 'Producto '.$product,
					        ));
						}
						else{ //sino saltamos la primera columna
							$this->addElement('hidden', 'production_quality_jump_channel_'.$channel.'_product_'.$product, array(
					            'decorators' => $this->emptyCellDecorators
					    	));	
						}
					// en cualquier caso: la segunda columna lleva las cabeceras de canal
					$this->addElement('hidden', 'product_units_product_'.$product.'_channel_'.$channel.'_header', array(
			            'decorators' => $this->headerInLineDecorators,
			            'label'      => 'Canal '.$channel,
			        ));
					//añadimos los selectores
					for ($region=1; $region<=3; $region++){
						$this->addElement('text', 'product'.$product.'_channel.'.$channel.'_region'.$region.'_production', array(
				            'decorators' => $this->oneCellInLineDecorators, 'attribs' => array('class' => 'small')
				        ));
					}//for region
					// Cierra Fila
					$this->addElement('hidden', 'production_units_close_row'.($product+$channel), array(
			            'decorators' => $this->closeRowDecorators
			    	));
				}// for channel
			}// for product
			$this->addElement('hidden', 'production_units_close', array(
		            'decorators' => $this->closeSectionDecorators
		    ));
		    
		}
	
	    
	
	}
?>