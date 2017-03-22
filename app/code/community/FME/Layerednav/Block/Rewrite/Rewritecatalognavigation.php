<?php

class FME_Layerednav_Block_Rewrite_Rewritecatalognavigation extends Infortis_UltraMegamenu_Block_Navigation
{

  /**
   * Get categories of current store
   *
   * @return Varien_Data_Tree_Node_Collection
   */
  public function getStoreCategories()
  {
      $helper = Mage::helper('layerednav/category');
      return $helper->getStoreCategories();
  }
}
