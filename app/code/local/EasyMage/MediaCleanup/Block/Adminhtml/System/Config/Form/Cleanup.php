<?php
/**
 * EasyMage_MediaCleanup
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 * Original code copyright (c) 2006-2016 X.commerce, Inc. (http://www.magento.com)
 *
 * @package    EasyMage_MediaCleanup
 * @author     Konstantin Dubovik
 * @contact    kodubovik@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EasyMage_MediaCleanup_Block_Adminhtml_System_Config_Form_Cleanup extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Create custom form element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $buttonHtml = $this->_getAddRowButtonHtml($this->__('Run Now'));
        return $buttonHtml;
    }


    /**
     * Add button to layout
     *
     * @param $title
     * @return mixed
     */
    protected function _getAddRowButtonHtml($title)
    {
        $url = Mage::helper('adminhtml')->getUrl("adminhtml/mediacleanup/run");
        $buttonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setLabel($this->__($title))
            ->setOnClick("window.location.href='".$url."'")
            ->toHtml();
        return $buttonHtml;
    }
}