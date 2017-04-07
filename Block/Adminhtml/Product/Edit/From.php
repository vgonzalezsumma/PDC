<?php
/**
 *
 * Code for version 2.1 or more
 */
namespace Magebay\Pdc\Block\Adminhtml\Product\Edit;

class From extends \Magento\Backend\Block\Template
{
	
    /**
     * @param \Magebay\Pdc\Helper\Data
     * 
     */
	public $_pdcHelper;
    /**
     * @param \Magebay\Pdc\Model\Productstatus
     * 
     */
    protected $actModelFactory;
	protected $productStatusFactory;
	protected $coreRegistry;
	protected $_product;
	protected $_productMetadata;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\App\ProductMetadataInterface $productMetadata,
		\Magento\Catalog\Model\Product $product,
        \Magebay\Pdc\Helper\Data $_pdcHelper,
        \Magebay\Pdc\Model\ProductstatusFactory $productStatus,
        \Magebay\Pdc\Model\ActFactory $actModelFactory,
        array $data = []
    ) {
		
        $this->setTemplate ( 'pdp/product/pdpdesign2.phtml' );
        parent::__construct($context, $data);
		
		$this->coreRegistry = $coreRegistry;
		$this->_productMetadata = $productMetadata;
		$this->_product = $product;
        $this->_pdcHelper = $_pdcHelper;
        $this->productStatusFactory = $productStatus;
        $this->actModelFactory = $actModelFactory;
    }
	function isShowTab() {
        $main_domain = $this->_pdcHelper->get_domain( $_SERVER['SERVER_NAME'] );
		if ( $main_domain != 'dev' ) {
            $rakes = $this->actModelFactory->create()->getCollection();
            $rakes->addFieldToFilter('path', 'pdp/act/key' );
            $valid = false;
            if ( count($rakes) > 0 ) {
                foreach ( $rakes as $rake )  {
                    if ( $rake->getExtensionCode() == md5($main_domain.trim($this->_pdcHelper->getStoreConfigData('pdp/act/key')) ) ) {
                        $valid = true;	
                    }
                }
            }		
            if ( $valid == true ) {
                //Check if module enable or not
                if($this->_pdcHelper->isModuleEnable()) {
                    //Check product type, check current action
                    $supportDesignProducts = array (
                        "simple",
                        "configurable",
                        "virtual",
                        "bundle"
                        //"downloadable" 
                    );
                    $product = $this->getCurrentProduct();
                    $productType = $product->getTypeId();
                    $currentAction = $this->getRequest()->getActionName();
                    if(!$product->isVisibleInSiteVisibility()) {
                        return false;
                    }
                    if(in_array($productType, $supportDesignProducts)) {
                        //show tab in edit action only
                        if($currentAction == "edit") {
                            return true;
                        } 
                    }
                }  
            }
		}
        return false;
    }
	public function getCurrentProduct() {
		$productId = $this->getRequest()->getParam('id');
        return $this->_product->load($productId);
    }
    public function getProductStatus() {
        $productId = $this->getCurrentProduct()->getId();
        $status = $this->productStatusFactory->create()->getProductStatus($productId);
        return $status;
    }
	function getBkMagentoVersion()
	{
		return $this->_productMetadata->getVersion();
	}
}
