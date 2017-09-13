<?php
class Zend_View_Helper_ScriptGenerator{
	function scriptGenerator($script, $options){
		$this->options=$options;
		switch ($script){
		  case 'show_hide_links':
		  	return $this->show_hide_links();
		  break;

		}
	}
	private function show_hide_links(){
		$id='"'.$this->options['id'].'"';
		return '<font class="show_hide_link"> (<a class="show_hide_link" href="javascript:void(0);" onClick=\'show('.$id.');\'>mostrar</a> / <a class="show_hide_link" href="javascript:void(0);" onClick=\'hide('.$id.');\'>ocultar</a>)</font>';
	}
	
}
?>