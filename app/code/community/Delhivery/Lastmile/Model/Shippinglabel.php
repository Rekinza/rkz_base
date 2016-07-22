<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Shipping Label Printing 
 */
class Delhivery_Lastmile_Model_Shippinglabel extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('lastmile/shippinglabel');
    }
    /**
     * Y coordinate
     *
     * @var int
     */
    public $y;

    /**
     * Zend PDF object
     *
     * @var Zend_Pdf
     */
    protected $_pdf;

	
    /**
     * Generate Shipment Label Content for each Waybill
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    public function getContent(&$page, $store = null, $waybill, $order, $pos)
    {
		$image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = $pos; //top border of the page
                $widthLimit  = 100; //half of the page width
                $heightLimit = 70; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 25;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);
				

				// Add Order ID, Date and COD amount
				$this->_setFontRegular($page, 7);
				$page->drawText(Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(), $x1+190, ($y1+25), 'UTF-8');
				$page->drawText(Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
                $order->getCreatedAtStoreDate(), 'medium', false), $x1+190, ($y1+15), 'UTF-8');
				$codamount = ($order->getPayment()->getMethodInstance()->getCode() == 'cashondelivery' ) ? $order->getGrandTotal() : "00.00";
				$page->drawText(Mage::helper('sales')->__('COD Amount ') . $codamount, $x1+190, ($y1+5), 'UTF-8');								

				// Add Barcode and waybill#
				//$fontPath = '/var/www/html/magento/barcode-fonts/FRE3OF9X.TTF';
				$fontPath = Mage::getBaseDir() . '/media/delhivery/font/FRE3OF9X.TTF';
				$page->setFont(Zend_Pdf_Font::fontWithPath($fontPath), 30);
				$barcodeImage = "*".$waybill."*";
				$page->drawText($barcodeImage, $x1+390, $y1+12);				
		        $this->_setFontRegular($page, 7);
				$page->drawText("*", $x1+385, $y1+15);
				$page->drawText("*", $x1+540, $y1+15);
				$page->drawText("AWB# $waybill", $x1+420, $y1+2);
				$this->_setFontBold($page, 8);
				$page->drawText("Ship to:", $x1+390, $y1-15);
				$page->drawText("From:", $x1, $y1-15);
				$this->_setFontRegular($page, 7);
				// Add Shipping Address
				$shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));				
				$addressy = $y1-25;
				foreach ($shippingAddress as $value){
					if ($value!=='') {
						$text = array();
						foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
							$text[] = $_value;
						}
						foreach ($text as $part) {
							$page->drawText(strip_tags(ltrim($part)), $x1+390, $addressy, 'UTF-8');
							$addressy -= 11;
						}
					}
				}
				$addressy = $y1-25;
				// Add Store Address				
				foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $value){
					if ($value !== '') {
						$value = preg_replace('/<br[^>]*>/i', "\n", $value);
						foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
							$page->drawText(strip_tags(trim($_value)), $x1, $addressy, 'UTF-8');	
							$addressy -= 11;
						}
					}
				}
				$page->drawLine($x1, $pos-160, $x1+550, $pos-158);							
            }
        }
    }
    /**
     * Set PDF object
     *
     * @param  Zend_Pdf $pdf
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected function _setPdf(Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Retrieve PDF object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->_pdf instanceof Zend_Pdf) {
            Mage::throwException(Mage::helper('sales')->__('Please define PDF object before using.'));
        }

        return $this->_pdf;
    }

    /**
     * Return PDF document
     *
     * @param  array $shipments
     * @return Zend_Pdf
     */
    public function getPdf()
    {
        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
		//$page  = $this->newPage($pdf);
		//$this->getContent($page, $shipment->getStore(), $waybill, $shipment->getOrder());
        return $pdf;
    }	

    /**
     * Format address
     *
     * @param  string $address
     * @return array
     */
    protected function _formatAddress($address)
    {
        $return = array();
        foreach (explode('|', $address) as $str) {
            foreach (Mage::helper('core/string')->str_split($str, 45, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }

    /**
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }	
}