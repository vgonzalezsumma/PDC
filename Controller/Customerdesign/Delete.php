<?php
 
namespace Magebay\Pdc\Controller\Customerdesign;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends \Magento\Framework\App\Action\Action
{
	protected $_customerSession;
	protected $_messageManager;
	protected $_customerdesign;
	
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magebay\Pdc\Model\CustomerdesignFactory $customerdesign
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_messageManager = $messageManager;
		$this->_customerdesign = $customerdesign;
    }
    public function execute()
    {
		$resultPageFactory = $this->resultPageFactory->create();
			// Add page title
		$resultPageFactory->getConfig()->getTitle()->set(__('Customize Product'));
		if(!$this->_customerSession->isLoggedIn())
		{
			$message = __("You have to login to Delete Your Design");
			$this->_messageManager->addError($message);
			$this->_redirect("*/*/index");
		}
		$params = $this->getRequest()->getParams();
		$id = isset($params['id']) ? $params['id'] : 0;
		$customerDesignModel = $this->_customerdesign->create();
		$okDelete = false;
		if($id > 0)
		{
			$checkCustomerDesign = $model = $customerDesignModel->load($id);
			if($checkCustomerDesign->getId() && $checkCustomerDesign->getCustomerId() == $this->_customerSession->getId())
			{
				$okDelete = true;
			}
		}
		if(!$okDelete)
		{
			$this->_messageManager->addError(__('You can not Delete data !'));
			$this->_redirect("*/*/index");
		}
        $model = $customerDesignModel->setId($id)->delete();
        $this->_messageManager->addSuccess(__('Data Have been Delete success'));
		$this->_redirect("*/*/index");
    }
}
 