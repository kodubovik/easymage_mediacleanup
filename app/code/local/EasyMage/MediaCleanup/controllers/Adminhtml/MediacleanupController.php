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
class EasyMage_MediaCleanup_Adminhtml_MediacleanupController extends Mage_Adminhtml_Controller_Action
{
    /**
     *  Run cleanup
     */
    public function runAction() {
        Mage::getModel('easymage_mediacleanup/cleanup')->cleanup(false);
        $this->_redirectReferer();
    }

    /**
     *  Run dry cleanup
     */
    public function dryrunAction() {
        Mage::getModel('easymage_mediacleanup/cleanup')->cleanup(true);
        $this->_redirectReferer();
    }
}