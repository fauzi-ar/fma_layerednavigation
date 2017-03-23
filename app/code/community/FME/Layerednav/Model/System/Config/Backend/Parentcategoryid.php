<?php

class FME_Layerednav_Model_System_Config_Backend_Parentcategoryid extends Mage_Core_Model_Config_Data
{	
	public function _afterSave()
    {
		//Get the saved value
		$value = $this->getValue();
		
		//Get the value from config (previous value)
		$oldValue = $this->getOldValue();
		
		if ($value != $oldValue)
		{
			if (empty($value) || trim($value) === '')
			{
				Mage::helper('layerednav')->__('Something\'s wrong');
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addNotice(
					Mage::helper('layerednav')->__('url Rewrite has been created.')
				);

				//create rewrite for Brand and Vendor
				Mage::helper('layerednav')->createBrandRewrite($value);
				Mage::helper('layerednav')->createVendorRewrite($value);
			}
		}
		
        return parent::_afterSave();
    }
}
