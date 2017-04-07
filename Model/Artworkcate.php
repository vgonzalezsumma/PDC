<?php 
/**
* @package    Magebay_Pdc
* @version    2.0
* @author     Magebay Developer Team <magebay99@gmail.com>
* @website    http://www.productsdesignercanvas.com
* @copyright  Copyright (c) 2009-2016 MAGEBAY.COM. (http://www.magebay.com)
*/
namespace Magebay\Pdc\Model;
class Artworkcate extends \Magento\Framework\Model\AbstractModel {
    /**
     * Initialize resource model
     * @return void
     */
    public function _construct() {
        $this->_init('Magebay\Pdc\Model\ResourceModel\Artworkcate');
    }
    public function getOptionArray() {
		$arr_status = array (
				array ('value' => 1, 'label' => Mage::helper ( 'pdp' )->__ ( 'Enabled' ) ),
				array ('value' => 2, 'label' => Mage::helper ( 'pdp' )->__ ( 'Disabled' ) ) 
		);
		return $arr_status;
	}
	public function getArtworkCateCollection() {
		$collection = $this->getCollection();
		$collection->addFieldToFilter('status', 1);
		$collection->setOrder('position', 'ASC');
		return $collection;
	}
	public function getCategoryOptions() {
		$options = $this->getArtworkCateCollection();
		$data = array();
		foreach ($options as $option) {
			$data[$option->getId()] = $option->getTitle();
		}	
		return $data;
	}
	public function getCategoryFilterOptions() {
		$options = $this->getArtworkCateCollection();
		$data = array();
		$data[0] = 'All';
		foreach ($options as $option) {
			$data[$option->getId()] = $option->getTitle();
		}	
		return $data;
	}
	public function getDefaultArtCate () {
		$cateId = $this->getArtworkCateCollection()->getFirstItem()->getId();
		return $cateId;
	}
    public function getImageCategories() {
        $collection = $this->getCollection();
        $collection->addFieldToFilter("image_types", "image");
        $collection->addFieldToFilter("status", 1);
        return $collection;
    }
    public function getImageBackgroundCategories() {
        $collection = $this->getCollection();
        $collection->addFieldToFilter("image_types", "background");
        $collection->addFieldToFilter("status", 1);
        return $collection;
    }
}