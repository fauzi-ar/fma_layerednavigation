<?php

/**
 * FME Layered Navigation 
 * 
 * @category     FME
 * @package      FME_Layerednav 
 * @copyright    Copyright (c) 2010-2011 FME (http://www.fmeextensions.com/)
 * @author       FME (Kamran Rafiq Malik)  
 * @version      Release: 1.0.0
 * @Class        FME_Layerednav_Block_Layer_View
 * @Overwrite    Mage_Catalog_Block_Layer_View
 */
class FME_Layerednav_Block_Layer_View extends Mage_Catalog_Block_Layer_View {

    protected $_filterBlocks = null;
    protected $_helper = null;
    
    public function __construct() {
        parent::__construct();
        $this->_helper = Mage::helper('layerednav');
    }
    
    public function getStateInfo() { 
        $_hlp = $this->_helper;
        //Check the Layered Nav position (Search or Catalog pages)
        $ajaxUrl = '';

        $isHttps = Mage::app()->getStore()->isFrontUrlSecure() && Mage::app()->getRequest()->isSecure();

        if ($_hlp->isSearch()) {
            $ajaxUrl = Mage::getUrl('layerednav/front/search',array('_secure' => $isHttps));
        } elseif ($cat = $this->getLayer()->getCurrentCategory()) {
            $ajaxUrl = Mage::getUrl('layerednav/front/category', array('id' => $cat->getId(), '_secure' => $isHttps));
        }


        $ajaxUrl = $_hlp->stripQuery($ajaxUrl);
        $url = $_hlp->getContinueShoppingUrl();

        //Set the AJAX Pagination
        $pageKey = Mage::getBlockSingleton('page/html_pager')->getPageVarName();

        //Get parameters of serach
        $queryStr = $_hlp->getParams(true, $pageKey);
        if ($queryStr)
            $queryStr = substr($queryStr, 1);

        $this->setClearAllUrl($_hlp->getClearAllUrl($url));

        if (false !== strpos($url, '?')) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        
        $params = Mage::app()->getRequest()->getParams();
        
        if (!$this->getIsAjax()){
            if (isset($params['brand'])){
                $queryStr.='brand=' . $params['brand'];    
            }
            else if (isset($params['vendor'])){
                $queryStr.='vendor=' . $params['vendor'];    
            }            
        }        
        return array($url, $queryStr, $ajaxUrl);
    }

    public function bNeedClearAll() {
        return $this->_helper->bNeedClearAll();
    }

    protected function _prepareLayout() {
        $_hlp = $this->_helper;
        // Return an object of current category
        $category = Mage::registry('current_category');

        if ($category) {
            $currentCategoryID = $category->getId();
        } else {
            $currentCategoryID = null;
        }

        // Return session object
        $sessionObject = Mage::getSingleton('catalog/session');
        if ($sessionObject AND $lastCategoryID = $sessionObject->getLastCatgeoryID()) {
            if ($currentCategoryID != $lastCategoryID) {
                Mage::register('new_category', true);
            }
        }
        $sessionObject->setLastCatgeoryID($currentCategoryID);

        //Create Category Blocks    
        $this->createCategoriesBlock();

        //preload setting    
        $this->setIsRemoveLinks($_hlp->removeLinks());

        //Get $this->_getFilterableAttributes() Mage_Catalog_Block_Layer_View
        $filterableAttributes = $this->_getFilterableAttributes();


        $blocks = array();
        foreach ($filterableAttributes as $attribute) {

            $blockType = 'layerednav/layer_filter_attribute';

            if ($attribute->getFrontendInput() == 'price') {
                $blockType = 'layerednav/layer_filter_price';
            }

            $name = $attribute->getAttributeCode() . '_filter';

            $blocks[$name] = $this->getLayout()->createBlock($blockType)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute);

            $this->setChild($name, $blocks[$name]);
        }

        foreach ($blocks as $name => $block) {
            $block->init();
        }
        $this->getLayer()->apply();
        return Mage_Core_Block_Template::_prepareLayout();
    }

    protected function createCategoriesBlock() {
        
        $_hlp = $this->_helper;
        if ('none' != $_hlp->catStyle()) {
            $categoryBlock = $this->getLayout()->createBlock('layerednav/layer_filter_category')
                    ->setLayer($this->getLayer())
                    ->init();
            $this->setChild('category_filter', $categoryBlock);
        }
    }

    /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters()
    {
        if (is_null($this->_filterBlocks)) {
            $filters = array();
            if ($categoryFilter = $this->_getCategoryFilter()) {
                $filters[] = $categoryFilter;
            }

            $filterableAttributes = $this->_getFilterableAttributes();
            $params = Mage::app()->getRequest()->getParams();

            foreach ($filterableAttributes as $attribute) {
                if (isset($params[$attribute->getAttributeCode()])) continue; //if brand exist in param, don't show in filter
                $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter');
            }
            $this->_filterBlocks =  $filters;
        }
        return $this->_filterBlocks;
    }

    protected function _toHtml() {
        $html = parent::_toHtml();
        if (!Mage::app()->getRequest()->isXmlHttpRequest()) {
            $html = '<div class="block block-layered-nav" id="block-layered-nav-reference">' . $html . '</div>';
            $html = '<div id="layered-nav-marker"></div>' . $html;
        }
        return $html;
    }

    public function getIsSearch(){
        return false;
    }

    public function getIsAjax(){
        if (Mage::app()->getRequest()->isXmlHttpRequest()) return true;

        return false;
    }
}
