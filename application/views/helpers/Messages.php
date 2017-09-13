<?php
class Zend_View_Helper_Messages{
	protected $messages=array(
		'production_capacity_insufficient'=>'La capacidad de la fábrica no es suficiente para fabricar las unidades pedidas.',
		'production_staff_insufficient'=>'No se ha podido contratar a todo el personal necesario.',
		'production_rm_insufficient'=>'El abastecimiento de materias primas ha sido insuficiente'
	);
	function direct($message_id){
		if (array_key_exists($message_id, $messages)){
			return $this->_messages[$message_id];
		}
		else{
			return $this->_message['message_not_defined'];
		}
	}
}

?>