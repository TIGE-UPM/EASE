<?php
	class Form_Marketing extends Form_Base{

	    public function loadDefaultDecorators()
	    {
	        $this->setDecorators(array(
	            'FormElements',
	            'Form',
	        ));
	    }
		public function init(){
			// Precios
			$this->addElement('hidden', 'marketing_prices_header', array(
			            'decorators' => $this->openSectionDecorators,
			            'label'       => 'Precios ',
			));
				// Nueva Fila
				$this->addElement('hidden', 'marketing_prices_open_row1', array(
			            'decorators' => $this->openRowDecorators,
				));
					// Salto 2 celdas
					$this->addElement('hidden', 'marketing_prices_jump1_row1', array(
			            'decorators' => $this->emptyCellDecorators
			    	));
					$this->addElement('hidden', 'marketing_prices_jump2_row1', array(
		            	'decorators' => $this->emptyCellDecorators
		    		));
					// Cabeceras Regiones
					for ($region=1; $region<=3; $region++){
						$this->addElement('hidden', 'product_prices_region_'.$region.'_header', array(
				            'decorators' => $this->headerInLineDecorators,
				            'label'       => 'Región '.$region,
				        ));
					}
				// Cierra Fila
				$this->addElement('hidden', 'marketing_prices_close_row1', array(
		            'decorators' => $this->closeRowDecorators
		    	));
			for ($product=1; $product<=3; $product++){
				for ($channel=1; $channel<=3; $channel++){
					// Nueva Fila
					$this->addElement('hidden', 'marketing_prices_open_row'.(3*($product-1)+$channel), array(
				            'decorators' => $this->openRowDecorators,
					));
						// Si es la central: ponemos etiqueta de producto
						if ($channel==2){
							$this->addElement('hidden', 'product_price_product_'.$product.'_header', array(
					            'decorators' => $this->headerInLineDecorators,
					            'label'       => 'Producto '.$product,
					        ));
						}
						else{ //sino saltamos la primera columna
							$this->addElement('hidden', 'marketing_prices_jump_channel_'.$channel.'_product_'.$product, array(
					            'decorators' => $this->emptyCellDecorators
					    	));	
						}
					// en cualquier caso: la segunda columna lleva las cabeceras de canal
					$this->addElement('hidden', 'product_price_product_'.$product.'_channel_'.$channel.'_header', array(
			            'decorators' => $this->headerInLineDecorators,
			            'label'      => 'Canal '.$channel,
			        ));
					//añadimos los selectores
					for ($region=1; $region<=3; $region++){
						$this->addElement('text', 'product'.$product.'_channel.'.$channel.'_region'.$region.'_price', array(
				            'decorators' => $this->oneCellInLineDecorators, 'attribs' => array('class' => 'small')
				        ));
					}//for region
					// Cierra Fila
					$this->addElement('hidden', 'marketing_prices_close_row'.($product+$channel), array(
			            'decorators' => $this->closeRowDecorators
			    	));
				}// for channel
			}// for product
			$this->addElement('hidden', 'marketing_prices_close', array(
		            'decorators' => $this->closeSectionDecorators
		    ));

			// Publicidad
			$this->addElement('hidden', 'marketing_advertising_header', array(
			            'decorators' => $this->openSectionDecorators,
			            'label'       => 'Publicidad ',
			));
				// Nueva Fila
				$this->addElement('hidden', 'marketing_advertising_open_row1', array(
			            'decorators' => $this->openRowDecorators,
				));
					// Salto 2 celdas
					$this->addElement('hidden', 'marketing_advertising_jump1_row1', array(
			            'decorators' => $this->emptyCellDecorators
			    	));
					$this->addElement('hidden', 'marketing_advertising_jump2_row1', array(
		            	'decorators' => $this->emptyCellDecorators
		    		));
					// Cabeceras Regiones
					for ($region=1; $region<=3; $region++){
						$this->addElement('hidden', 'product_advertising_region_'.$region.'_header', array(
				            'decorators' => $this->headerInLineDecorators,
				            'label'       => 'Región '.$region,
				        ));
					}
				// Cierra Fila
				$this->addElement('hidden', 'marketing_advertising_close_row1', array(
		            'decorators' => $this->closeRowDecorators
		    	));
			for ($product=1; $product<=3; $product++){
				for ($media=1; $media<=3; $media++){
					// Nueva Fila
					$this->addElement('hidden', 'marketing_advertising_open_row'.(3*($product-1)+$media), array(
				            'decorators' => $this->openRowDecorators,
					));
						// Si es la central: ponemos etiqueta de producto
						if ($media==2){
							$this->addElement('hidden', 'product_advertising_product_'.$product.'_header', array(
					            'decorators' => $this->headerInLineDecorators,
					            'label'       => 'Producto '.$product,
					        ));
						}
						else{ //sino saltamos la primera columna
							$this->addElement('hidden', 'marketing_advertising_jump_media_'.$media.'_product_'.$product, array(
					            'decorators' => $this->emptyCellDecorators
					    	));	
						}
					// en cualquier caso: la segunda columna lleva las cabeceras de canal
					$this->addElement('hidden', 'product_advertising_product_'.$product.'_media_'.$media.'_header', array(
			            'decorators' => $this->headerInLineDecorators,
			            'label'      => 'Media '.$media,
			        ));
					//añadimos los selectores
					for ($region=1; $region<=3; $region++){
						$this->addElement('select', 'product'.$product.'_media.'.$media.'_region'.$region.'_advertising', array(
				            'decorators' => $this->oneCellInLineDecorators, 'attribs' => array('class' => 'small'),
							'multiOptions' =>array(0=>'Ninguna',1=>'Baja', 2=>'Media', 3=>'Alta')
				        ));
					}//for region
					// Cierra Fila
					$this->addElement('hidden', 'marketing_advertising_close_row'.($product+$media), array(
			            'decorators' => $this->closeRowDecorators
			    	));
				}// for media
			}// for product
			$this->addElement('hidden', 'marketing_advertising_close', array(
		            'decorators' => $this->closeSectionDecorators
		    ));
		    
		    			// Trade MKT
			$this->addElement('hidden', 'marketing_trademkt_header', array(
			            'decorators' => $this->openSectionDecorators,
			            'label'       => 'Trade MKT ',
			));
				// Nueva Fila
				$this->addElement('hidden', 'marketing_trademkt_open_row1', array(
			            'decorators' => $this->openRowDecorators,
				));
					// Salto 2 celdas
					$this->addElement('hidden', 'marketing_trademkt_jump1_row1', array(
			            'decorators' => $this->emptyCellDecorators
			    	));
					$this->addElement('hidden', 'marketing_trademkt_jump2_row1', array(
		            	'decorators' => $this->emptyCellDecorators
		    		));
					// Cabeceras Regiones
					for ($region=1; $region<=3; $region++){
						$this->addElement('hidden', 'product_trademkt_region_'.$region.'_header', array(
				            'decorators' => $this->headerInLineDecorators,
				            'label'       => 'Región '.$region,
				        ));
					}
				// Cierra Fila
				$this->addElement('hidden', 'marketing_trademkt_close_row1', array(
		            'decorators' => $this->closeRowDecorators
		    	));
			for ($product=1; $product<=3; $product++){
				for ($trademedia=1; $trademedia<=2; $trademedia++){
					// Nueva Fila
					$this->addElement('hidden', 'marketing_trademkt_open_row'.(3*($product-1)+$trademedia), array(
				            'decorators' => $this->openRowDecorators,
					));
						// Si es la central: ponemos etiqueta de producto
						if ($trademedia==2){
							$this->addElement('hidden', 'product_trademkt_product_'.$product.'_header', array(
					            'decorators' => $this->headerInLineDecorators,
					            'label'       => 'Producto '.$product,
					        ));
						}
						else{ //sino saltamos la primera columna
							$this->addElement('hidden', 'marketing_trademkt_jump_trademedia_'.$trademedia.'_product_'.$product, array(
					            'decorators' => $this->emptyCellDecorators
					    	));	
						}
					// en cualquier caso: la segunda columna lleva las cabeceras de canal
					$this->addElement('hidden', 'product_trademkt_product_'.$product.'_trademedia_'.$trademedia.'_header', array(
			            'decorators' => $this->headerInLineDecorators,
			            'label'      => 'Trade Media '.$trademedia,
			        ));
					//añadimos los selectores
					for ($region=1; $region<=3; $region++){
						$this->addElement('select', 'product'.$product.'_trademedia.'.$trademedia.'_region'.$region.'_trademkt', array(
				            'decorators' => $this->oneCellInLineDecorators, 'attribs' => array('class' => 'small'),
							'multiOptions' =>array(0=>'Ninguna',1=>'Baja', 2=>'Media', 3=>'Alta')
				        ));
					}//for region
					// Cierra Fila
					$this->addElement('hidden', 'marketing_trademkt_close_row'.($product+$trademedia), array(
			            'decorators' => $this->closeRowDecorators
			    	));
				}// for trademedia
			}// for product
			$this->addElement('hidden', 'marketing_trademkt_close', array(
		            'decorators' => $this->closeSectionDecorators
		    ));
		    
		    
		    
		}
	
	    
	
	}
?>