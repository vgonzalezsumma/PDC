<?php 
namespace Magebay\Pdc\Block;
use Magebay\Pdc\Helper\Data as PdcHelper;
use Magento\Framework\App\Request\Http;
class Designbutton extends \Magento\Framework\View\Element\Template {
    protected $_currentAction;
	protected $_shareId;
    protected $_extraOption;
    protected $_params;
    public $pdcHelper;
    protected $_registry;
    protected $productFactory;
    protected $request;
    protected $currency;
    protected $adminTemplateFactory;
	protected $_pdcShare;
	protected $_customerdesign;
	protected $_customerSession;
	protected $_coreCart;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        PdcHelper $pdcHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Http $request,
        \Magento\Directory\Model\Currency $currency, 
        \Magebay\Pdc\Model\AdmintemplateFactory $adminTemplateFactory,
        \Magebay\Pdc\Model\Share $pdcShare,
		\Magebay\Pdc\Model\Customerdesign $customerdesign,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Checkout\Model\Cart $coreCart,
        array $data = []
    )
    {
        $this->pdcHelper = $pdcHelper;
        $this->_registry = $registry;
        $this->productFactory = $productFactory;
        $this->request = $request;
        $this->_params = $this->request->getParams();
        $this->currency = $currency;
        $this->_pdcShare = $pdcShare;
        $this->_customerdesign = $customerdesign;
        $this->_customerSession = $customerSession;
        $this->adminTemplateFactory = $adminTemplateFactory;
        $this->_coreCart = $coreCart;
        if (isset($this->_params['action'])) {
            $this->_currentAction = $this->_params['action'];
        }
        if (isset($this->_params['share'])) {
            $this->_shareId = $this->_params['share'];
        }
        return parent::__construct($context, $data);
    }
    public function getParams() {
        return $this->_params;
    }
    public function getProductConfig($productId) {
        return $this->pdcHelper->getProductConfig($productId);
    }
    public function getCurrentCurrencySymbol() {
        return $this->currency->getCurrencySymbol();
    }
	public function getCurrentProduct() {
        $params = $this->_params;
        $currentProduct =  $this->_registry->registry('current_product');
        if ($currentProduct != NULL) {
            return $currentProduct;
        } else {
            if (isset($params['product-id'])) {
                $productId = $params['product-id'];
                $currentProduct = $this->productFactory->create()->load($productId);
                return $currentProduct;
            }
        }
        return null;
    }
    public function getProductId() {
        $currentProduct = $this->getCurrentProduct();
        if ($currentProduct != null) {
            return $currentProduct->getId();
        }
    }
    public function getProductUrl() {
        return $this->getCurrentProduct()->getProductUrl();
    }
    public function getProductName() {
        return $this->getCurrentProduct()->getName();
    }
    public function isDesignAble() {
        return $this->pdcHelper->isDesignAble($this->getProductId());
    }
    public function getPdpDesignInfo() {
        $response = array();
		//$helper = Mage::helper('pdp');
        $response['action'] = $this->_currentAction;
        $response['share_id'] = $this->_shareId;
        $response['cart_item_id'] = '';
        $response['wishlist_item_id'] = '';
        $response['template_id'] = '';
        $response['extra_options'] = '';
		$response['extra_options_value'] = '';
        $moduleName = $this->request->getModuleName();
        if ($this->request->getActionName() == "configure") {
            if ($moduleName == "checkout") {
                $itemCartId = $this->_params['id'];
                $response['cart_item_id'] = $itemCartId;
                // $cart = Mage::getModel('checkout/cart')->getQuote();
                $cart = $this->_coreCart->getQuote();
                $item = $cart->getItemById($itemCartId);
                $buyRequest = $item->getBuyRequest()->getData();
                if (isset($buyRequest['extra_options'])) {
                    $response['extra_options'] = $buyRequest['extra_options'];
					$response['extra_options_value'] = $this->pdcHelper->getPDPJsonContent($response['extra_options']);
                }
            } else if ($moduleName == "wishlist") {
                $itemWishlistId = $this->_params['id'];
                $response['wishlist_item_id'] = $itemWishlistId;
                $wishlist = Mage::getModel('wishlist/item_option');
                $item = $wishlist->load($itemWishlistId);
                $optionValue = $item->getValue();
                $buyRequest = unserialize($optionValue);
                if (isset($buyRequest['extra_options'])) {
                    $response['extra_options'] = $buyRequest['extra_options'];
					$response['extra_options_value'] = $this->pdcHelper->getPDPJsonContent($response['extra_options']);
                }
            }
            $response['action'] = "edit_cart_item";
        } else if ($this->_shareId != null) {
			$response['extra_options'] = $this->_pdcShare->load($this->_shareId)->getPdpdesign();
			$response['extra_options_value'] = $this->pdcHelper->getPDPJsonContent($response['extra_options']);
            $response['action'] = "share_design";
        } else if(isset($this->_params['redesign']) && $this->_params['redesign']) {
            //echo "Redesign Request";
            if($this->_customerSession->isLoggedIn()) {
                $customerDesign = $this->_customerdesign->load($this->_params['redesign']);
                if($customerDesign->getId()) {
                    //Zend_Debug::dump($customerDesign->getData());
                    $response['extra_options'] = $customerDesign->getFilename();
                    $response['extra_options_value'] = $this->pdcHelper->getPDPJsonContent($response['extra_options']);
                }
                $response['action'] = "redesign";
            }
        } else if (isset($this->_params['template-id'])) {
            if($this->_params['template-id']) {
                $templateJsonFilename = $this->adminTemplateFactory->create()->load($this->_params['template-id'])->getPdpDesign();
                if($templateJsonFilename != "") {
                    $response['extra_options'] = $templateJsonFilename;
                    $response['extra_options_value'] = $this->pdcHelper->getPDPJsonContent($response['extra_options']);
                    $response['template_id'] = $this->_params['template-id'];
                }
            }
        } else {
            //Get sample design if exists
        	$sampleDesignJsonFile = $this->pdcHelper->getSampleJsonFile($this->getProductId());
        	if ($sampleDesignJsonFile != "") {
        		$response['extra_options'] = $sampleDesignJsonFile;
        		$response['extra_options_value'] = $this->pdcHelper->getPDPJsonContent($response['extra_options']);
        	}
        }
        return $response;
    }
    public function getButtonLabel() {
    	// $buttonLabel = Mage::getStoreConfig("pdp/setting/design_button_label");
    	// if ($buttonLabel == "") {
    	// 	$buttonLabel = "Customize it!";
    	// }
        $buttonLabel = "Customize it!";
    	return $buttonLabel;
    }
	public function getCurrentDesignResultImage($jsonContent) {
    	$jsonDecoded = json_decode($jsonContent, true);
    	if(!$jsonDecoded) {
    		return;
    	}
    	$images = array();
    	foreach($jsonDecoded as $side) {
            if(isset($side['sideSvg'])) {
                $images[] = array(
                    'side_name' => $side['label'],
                    'image_result' => $side['sideSvg']
                );
            }
            
    	}
    	if (!empty($images)) {
    		return $images;
    	}
    } 
	function getShareInfo($shareId)
	{
		$shareInfo = $this->_pdcShare->load($shareId)->getData();
		return $shareInfo;
	}
}