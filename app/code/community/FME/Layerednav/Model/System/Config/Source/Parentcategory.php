<?php

class FME_Layerednav_Model_System_Config_Source_Parentcategory extends Varien_Object
{
    public function toOptionArray()
    {
        $category = Mage::getModel('catalog/category')->load(3);

        $subcategoryIds = $category->getChildren();
        $categoryCollection = Mage::getModel('catalog/category')->getCollection();
        $categoryCollection->addIdFilter($subcategoryIds);
        $categoryCollection->addAttributeToSelect('name');
        $options = array();

        foreach ($categoryCollection as $cat){
            $options[] = array(
                'value'=> $cat->getId(),
                'label' => $cat->getName()
            );
        }
        
        return $options;
    }
} 