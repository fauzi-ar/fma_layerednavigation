<?php
Class FME_Layerednav_Model_Observer
{
	public function attributechanged($observer){
		$type = $observer->getDataObject()->getData('attribute_code');

		if ($type == 'brand' || $type == 'vendor'){
			$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $type);
			$_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
			                ->setAttributeFilter($attributeModel->getId())
			              ->setStoreFilter(0)
			              ->load();

			$options = $_collection->toOptionArray();

			foreach ($options as $option){
				$code = strtolower(preg_replace('#[^0-9a-z ]+#i', '', $option['label']));
				$code = str_replace(' ', '-', trim($code));
				$rewrite = Mage::getModel('core/url_rewrite')->setStoreId(1)->loadByRequestPath($type . '/' . $code);
				if (!($rewrite['url_rewrite_id'])){
					Mage::getModel('core/url_rewrite')
					    ->setIsSystem(false)
					    ->setIdPath($type . '_' . $code . '_' . $option['value'])
					    ->setTargetPath('catalog/category/view/id/2003/'. $type .'/' . $option['value'])
					    ->setRequestPath($type . '/' . $code)
					    ->save();
				}
				//overwrite the old one
				else {
					$rewrite->setIdPath($type . '_' . $code . '_' . $option['value'])
							->setTargetPath('catalog/category/view/id/2003/'. $type .'/' . $option['value'])
							->save();
				}
			}


		}
	}
}
