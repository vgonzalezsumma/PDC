<?php
 
namespace Magebay\Pdc\Controller\Customerdesign;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Index extends \Magento\Framework\App\Action\Action
{
	protected $_customerSession;
	protected $_messageManager;
	
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_messageManager = $messageManager;
    }
    public function execute()
    {
		$resultPageFactory = $this->resultPageFactory->create();
			// Add page title
		$resultPageFactory->getConfig()->getTitle()->set(__('Customize Product'));
		if(!$this->_customerSession->isLoggedIn())
		{
			$message = __("You have to login before see your design");
			$this->_messageManager->addError($message);
		}
		return $resultPageFactory;
    }
}
 