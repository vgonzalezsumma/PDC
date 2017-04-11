<?php
namespace Magebay\Pdc\Controller\Test;
use Magebay\Pdc\Model\Upload;
use Magento\Store\Model\StoreManagerInterface;
use Magebay\Pdc\Helper\Data as PdcHelper;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Index extends \Magento\Framework\App\Action\Action {
    protected $uploadModel;
    protected $storeManager;
    protected $pdcHelper;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Upload $upload,
        StoreManagerInterface $storeManager,
        PdcHelper $pdcHelper
    )
    {
        $this->uploadModel = $upload;
        $this->storeManager = $storeManager;
        $this->pdcHelper = $pdcHelper;
        parent::__construct($context);
    }
    public function execute() {
        echo "<pre>";
        $model = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $model->load(2);
        //\Zend_Debug::dump(get_class_methods($model));
        \Zend_Debug::dump($model->getTierPrice(3));
        
    }
}
