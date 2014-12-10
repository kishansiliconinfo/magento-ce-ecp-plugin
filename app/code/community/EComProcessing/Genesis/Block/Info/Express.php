<?php

class EComProcessing_Genesis_Block_Info_Express extends Mage_Payment_Block_Info
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ecomprocessing/info/express.phtml');
	}

	public function getMethodCode()
	{
		return $this->getInfo()->getMethodInstance()->getCode();
	}
}