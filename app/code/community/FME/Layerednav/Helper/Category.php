<?php

class FME_Layerednav_Helper_Category extends Mage_Catalog_Helper_Category
{
	public function getStoreCategories($sorted=false, $asCollection=false, $toLoad=true)
	{
	    $parent     = Mage::getStoreConfig('layerednav/layerednav/catalog_parent_category_id');
	    $cacheKey   = sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
	    if (isset($this->_storeCategories[$cacheKey])) {
	        return $this->_storeCategories[$cacheKey];
	    }

	    /**
	     * Check if parent node of the store still exists
	     */
	    $category = Mage::getModel('catalog/category');
	    /* @var $category Mage_Catalog_Model_Category */
	    if (!$category->checkId($parent)) {
	        if ($asCollection) {
	            return new Varien_Data_Collection();
	        }
	        return array();
	    }

	    $recursionLevel  = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));
	    $storeCategories = $category->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);

	    $this->_storeCategories[$cacheKey] = $storeCategories;
	    return $storeCategories;
	}
}
