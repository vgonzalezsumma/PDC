<?php 
/**
* @package    Magebay_Pdc
* @version    2.0
* @author     Magebay Developer Team <magebay99@gmail.com>
* @website    http://www.productsdesignercanvas.com
* @copyright  Copyright (c) 2009-2016 MAGEBAY.COM. (http://www.magebay.com)
*/
namespace Magebay\Pdc\Model;
class Jsonfile extends \Magento\Framework\Model\AbstractModel {
    /**
     * Initialize resource model
     * @return void
     */
    public function _construct() {
        $this->_init('Magebay\Pdc\Model\ResourceModel\Jsonfile');
    }
}