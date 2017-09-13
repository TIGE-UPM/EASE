<?php
	class Form_Base extends Zend_Form{
		public $paragraphedDecorators = array(
		'ViewHelper',
		'Errors',
		'Label',
		array(array('data' => 'HtmlTag'), array('tag' => 'p', 'class' => 'general'))
		);
		public $buttonDecorators = array(
		'ViewHelper',
		'Errors',
		);
		public $elementDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('Label', array('tag' => 'tr')),
		);
		public $elementInLineDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('Label', array('tag' => 'td', 'class'=>'headerInLine')),
		);
		public $oneCellInLineDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		);
		public $headerInLineDecorators = array(
		'ViewHelper',
		'Errors',
		'Label',
		array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'label'))
		);
		public $openSectionDecorators = array(
		'ViewHelper',
		'Errors',
		array('Label', array('tag' => 'th')),
		array(array('table' => 'HtmlTag'), array('tag' => 'table', 'openOnly'=>true)),
		);
		public $closeSectionDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'table', 'closeOnly'=>true)),
		);
		public $openRowDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'tr', 'openOnly'=>true)),
		);
		public $open_row_and_groupDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'tr', 'class'=>'openGroup', 'openOnly'=>true)),
		);
		public $closeRowDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly'=>true)),
		);
		
		public $emptyCellDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('data' => 'HtmlTag'), array('tag' => 'td')),
		);
	

	}
?>