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
class EasyMage_MediaCleanup_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Path to media cleanup extensions
     */
    const XML_MEDIACLEANUP_EXTENSIONS = 'mediacleanup_options/general/extensions';

    /**
     * Parse extensions from config
     *
     * @return array|mixed|null|string
     */
    public function parseExt() {
        $config = Mage::app()->getStore()->getConfig(self::XML_MEDIACLEANUP_EXTENSIONS);
        $config = str_replace(' ', '', $config);
        $config = explode(',', $config);
        return $config;
    }

    /**
     * Get images from directory
     *
     * @param array $ext
     * @param $dir
     * @return RegexIterator
     */
    public function getFiles(array $ext, $dir) {
        $directory = new RecursiveDirectoryIterator($dir);
        $iterator = new RecursiveIteratorIterator($directory);
        $regexp = $this->prepareRegexp($ext);
        $files = new RegexIterator($iterator, $regexp, RecursiveRegexIterator::GET_MATCH);
        return $files;
    }

    /**
     * Prepare regular expression for matching images
     *
     * @param array $exts
     * @return string
     */
    public function prepareRegexp(array $exts) {
        $regexp = '/^.+\.(';
        $count = count($exts);
        foreach($exts as $index => $ext) {
            if($ext == 'jpg' || $ext == 'jpeg') $ext = 'jpe?g';
            if($count == 0 || $index == ($count - 1)) {
                $regexp .= $ext . ')$/i';
            }
            else {
                $regexp .= $ext . '|';
            }
        }
        return $regexp;
    }

    /**
     * Send email
     *
     * @param $recipient
     * @param $subject
     * @param $message
     * @throws Zend_Mail_Exception
     */
    public function sendEmail($recipient, $subject, $message) {
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($message);
        $mail->setFrom(Mage::getStoreConfig('trans_email/ident_general/email'));
        $mail->addTo($recipient, 'No reply');
        $mail->setSubject($subject);
        $mail->send();
    }
}