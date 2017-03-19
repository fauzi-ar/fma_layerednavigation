<?php 

class FME_Layerednav_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup{

	protected function createBrandRewrite(){
		
		$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'brand');
		$_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
		                ->setAttributeFilter($attributeModel->getId())
		              ->setStoreFilter(0)
		              ->load();

		$options = $_collection->toOptionArray();
			
/*		SCRIPT FOR DELETING REWRITE */
/*
		$rewrites = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('id_path',array('like' => 'brand_%'));
		foreach ($rewrites as $rewrite){
			$rewrite->delete();
		}
		echo 'delete success';die();
*/
		foreach ($options as $option){
			$code = strtolower(preg_replace('#[^0-9a-z]+#i', '-', $option['label']));
			$rewriteBrand = Mage::getModel('core/url_rewrite')->setStoreId(1)->loadByRequestPath('brand/' . $code);
			if (!($rewriteBrand['url_rewrite_id'])){
				Mage::getModel('core/url_rewrite')
				    ->setIsSystem(false)
				    ->setIdPath('brand_' . $code . '_' . $option['value'])
				    ->setTargetPath('catalog/category/view/id/2003/brand/' . $option['value'])
				    ->setRequestPath('brand/' . $code)
				    ->save();
			}
		}
	}

	protected function createVendorRewrite(){
		
		$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'vendor');
		$_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
		                ->setAttributeFilter($attributeModel->getId())
		              ->setStoreFilter(0)
		              ->load();

		$options = $_collection->toOptionArray();
			
/*		SCRIPT FOR DELETING REWRITE */
/*
		$rewrites = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('id_path',array('like' => 'vendor_%'));
		foreach ($rewrites as $rewrite){
			$rewrite->delete();
		}
		echo 'delete success';die();
*/
		foreach ($options as $option){
			$code = strtolower(preg_replace('#[^0-9a-z]+#i', '-', $option['label']));
			$rewriteBrand = Mage::getModel('core/url_rewrite')->setStoreId(1)->loadByRequestPath('vendor/' . $code);
			if (!($rewriteBrand['url_rewrite_id'])){
				Mage::getModel('core/url_rewrite')
				    ->setIsSystem(false)
				    ->setIdPath('vendor_' . $code . '_' . $option['value'])
				    ->setTargetPath('catalog/category/view/id/2003/vendor/' . $option['value'])
				    ->setRequestPath('vendor/' . $code)
				    ->save();
			}
		}
	}

} ?>