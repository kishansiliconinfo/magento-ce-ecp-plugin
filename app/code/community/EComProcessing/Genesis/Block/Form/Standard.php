<?php

class EComProcessing_Genesis_Block_Form_Standard extends Mage_Payment_Block_Form
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ecomprocessing/form/standard.phtml');
	}
}