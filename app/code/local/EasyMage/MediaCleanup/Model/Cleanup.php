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
class EasyMage_MediaCleanup_Model_Cleanup extends Varien_Object
{
    /**
     *  Path to media cleanup enable flag
     */
    const XML_MEDIACLEANUP_ENABLE = 'mediacleanup_options/general/enable';

    /**
     *  Path to media cleanup image dir config
     */
    const XML_MEDIACLEANUP_DIR = 'mediacleanup_options/general/img_dir';

    /**
     *  Path to media cleanup skip cache flag
     */
    const XML_MEDIACLEANUP_SKIP_CACHE = 'mediacleanup_options/general/skip_cache';

    /**
     *  Path to media cleanup enable email notification flag
     */
    const XML_MEDIACLEANUP_ENABLED_EMAIL = 'mediacleanup_options/general/enabled_email';

    /**
     *  Path to media cleanup email config
     */
    const XML_MEDIACLEANUP_EMAIL = 'mediacleanup_options/general/email';

    /**
     *  Path to media cleanup enable cron flag
     */
    const XML_MEDIACLEANUP_ENABLE_CRON = 'mediacleanup_options/general/enable_cron';

    /**
     * Database read connection
     *
     * @var
     */
    protected $_read;

    /**
     * Admin session
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_session;

    /**
     * Module helper
     *
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * Module config array
     *
     * @var array
     */
    protected $_config;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_session = Mage::getSingleton('adminhtml/session');
        $this->_helper = Mage::helper('easymage_mediacleanup');
        $this->_config = array(
            'enable' => Mage::app()->getStore()->getConfig(self::XML_MEDIACLEANUP_ENABLE),
            'media_dir' => Mage::app()->getStore()->getConfig(self::XML_MEDIACLEANUP_DIR),
            'skip_cache' => Mage::app()->getStore()->getConfig(self::XML_MEDIACLEANUP_SKIP_CACHE),
            'enabled_email' => Mage::app()->getStore()->getConfig(self::XML_MEDIACLEANUP_ENABLED_EMAIL),
            'email' => Mage::app()->getStore()->getConfig(self::XML_MEDIACLEANUP_EMAIL),
            'enable_cron' => Mage::app()->getStore()->getConfig(self::XML_MEDIACLEANUP_ENABLE_CRON)
        );
    }

    /**
     * Media cleanup
     *
     * @param bool|false $dryrun
     */
    public function cleanup($dryrun = false) {
        if($this->_config['enable']) {
            //find images in media directory
            try {
                $media = Mage::getBaseDir() . $this->_config['media_dir'];
                $exts = Mage::helper('easymage_mediacleanup')->parseExt();
                $images = Mage::helper('easymage_mediacleanup')->getFiles($exts, $media);

                //find images saved to database
                $dbImages = $this->_getDbImg();

                //cleanup media directory
                $removedCount=0;
                $skippedCount=0;
                foreach($images as $image) {
                    if(strpos($image[0],'cache') != false && $this->_config['skip_cache']) continue;

                    $imageCleanup = str_replace($media, '', $image[0]);
                    if(isset($dbImages[$imageCleanup]))
                    {
                        $skippedCount++;
                        continue;
                    }
                    else
                    {
                        if($dryrun == false) {
                            unlink($image[0]);
                            $this->_removed[] = $image[0];
                        }
                        $removedCount++;
                    }
                }
                if($dryrun) {
                    $this->_session->addSuccess($this->_helper->__("Done, %s images found to be removed and %s images found to be skipped.", $removedCount, $skippedCount));
                } else {
                    $this->_session->addSuccess($this->_helper->__("Done, removed %s images and skipped %s images.", $removedCount, $skippedCount));
                    if($this->_config['enabled_email'])
                    {
                        $this->_helper->sendEmail(
                            $this->_config['email'],
                            $this->_helper->__('Media cleanup success report %s', Mage::getModel('core/date')->date('Y-m-d H:i:s')),
                            $this->_helper->__("Removed %s images and skipped %s images.", $removedCount, $skippedCount)
                        );
                    }
                }
            } catch (Exception $e) {
                $this->_session->addError($e->getMessage());
                if($this->_config['enabled_email'])
                {
                    $this->_helper->sendEmail(
                        $this->_config['email'],
                        $this->_helper->__('Media cleanup failure report %s', Mage::getModel('core/date')->date('Y-m-d H:i:s')),
                        $e->getMessage()
                    );
                }
            }
        }
    }

    /**
     * Run cleanup via cron
     */
    public function runCron()
    {
        if($this->_config['enable_cron']) {
            $this->cleanup(false);
        }
    }

    /**
     * Get images saved to the database
     *
     * @return array
     */
    protected function _getDbImg()
    {
        $prefix = Mage::getConfig()->getTablePrefix();
        $query = "SELECT value FROM " . $prefix . "catalog_product_entity_media_gallery";
        $data = $this->_read->fetchAll($query);
        $result=array();
        foreach($data as $item){
            $result[$item['value']]=$item['value'];
        }
        return $result;
    }
}