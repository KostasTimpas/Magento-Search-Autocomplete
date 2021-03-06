<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Autocomplete queries list
 */
class Mage_CatalogSearch_Block_Autocomplete extends Mage_Core_Block_Abstract
{
    protected $_suggestData = null;

    protected function _toHtml()
    {
        $html = '';
        $current_store_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        if (!$this->_beforeToHtml()) {
            return $html;
        }

        $suggestData = $this->getSuggestData();
        if (!($count = count($suggestData))) {
            return $html;
        }

        $count--;

        $html = '<ul><li style="display:none"></li>';
        foreach ($suggestData as $index => $item) {
            if ($index == 0) {
                $item['row_class'] .= ' first';
            }

            if ($index == $count) {
                $item['row_class'] .= ' last';
            }

            $html .=  '<li title="'.$this->escapeHtml($item['title']).'" class="'.$item['row_class'].'">'
                . '<a href="'.$item['url_path'].'"><div style="max-width:30%; float:left;" class="product-photo-search"><img src="'.$item['image_url'].'" /></div><div style="max-width:69%; float:left; margin-top:6px;padding-left:3px" class="name-product-search"><span>'.$this->escapeHtml($item['title']). "<br />". "<span style='display:inline-block; margin-top:6px;' class='price-custom-span'>Price: " . number_format((float)$item['price'], 2, '.', '') . "" . $current_store_symbol .'</span></span></div></a></li>';
        }

        $html.= '</ul>';

        return $html;
    }

    public function getSuggestData()
    {
        if (!$this->_suggestData) {
            $query = $this->helper('catalogsearch')->getQueryText();
            $collection = Mage::getSingleton('catalog/product')->getCollection()
                            ->addAttributeToFilter('name', array(
                                'like' => '%'.$query.'%'
                            ))
                            ->addAttributeToSelect('price')
                            ->setPageSize(8)
                            ->addAttributeToSelect('small_image')
                            ->addAttributeToSelect('url_path')
                            ->addAttributeToFilter('visibility', 4);

            $counter = 0;
            $data = array();
            foreach ($collection as $item) {
                $_data = array(
                    'title' => $item->getName(),
                    'price' => $item->getPrice(),
                    'row_class' => (++$counter)%2?'odd':'even',
                    'image_url' => (string)Mage::helper('catalog/image')->init($item, 'small_image')->resize(70),
                    'url_path' => $item->getUrlPath()
                );

                if ($item->getName() == $query) {
                    array_unshift($data, $_data);
                }
                else {
                    $data[] = $_data;
                }
            }
            $this->_suggestData = $data;
        }
        return $this->_suggestData;
    }
/*
 *
*/
}
