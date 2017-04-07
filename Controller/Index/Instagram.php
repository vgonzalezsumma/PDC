<?php
 
namespace Magebay\Pdc\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\ScopeInterface;
require_once(BP . "/lib/instagram/instagram.php");
class Instagram extends \Magento\Framework\App\Action\Action
{
	protected $_scopeConfig;
	protected $_storeManager;
	protected $_viewContext;
    public function __construct(
        Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\View\Element\Template\Context $viewContext
    )
    {
        parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->_viewContext = $viewContext;
    }
    public function execute()
    {
		$baseUrl = $this->_storeManager->getStore()->getBaseUrl();
		$client_id = $this->_scopeConfig->getValue('pdp/customer_action/instagram_api',ScopeInterface::SCOPE_STORE); 
		$client_secret = $this->_scopeConfig->getValue('pdp/customer_action/instagram_key',ScopeInterface::SCOPE_STORE); 
		$redirect_uri = $baseUrl.'pdc/index/instagram';
		$instagram = new \InstagramUploader($client_id,$client_secret,$redirect_uri,$_GET['code']);
		$instagram ->init();
		$asset_repository = $this->_viewContext->getAssetRepository();
        $asset  = $asset_repository->createAsset('Magebay_Pdc::pdc/');	
		echo '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
		echo '<script type="text/javascript" src="'.$asset->getUrl().'/instagram/pdc_ins_results.js"></script>'; 
    }
}
 