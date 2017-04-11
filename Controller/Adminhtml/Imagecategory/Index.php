<?php
/**
* @package    Magebay_Pdc
* @version    2.0
* @author     Magebay Developer Team <magebay99@gmail.com>
* @website    http://www.productsdesignercanvas.com
* @copyright  Copyright (c) 2009-2016 MAGEBAY.COM. (http://www.magebay.com)
*/
namespace Magebay\Pdc\Controller\Adminhtml\Imagecategory;

class Index extends \Magebay\Pdc\Controller\Adminhtml\Imagecategory
{
    protected $resultPageFactory;
    protected $resultForwardFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context, $coreRegistry);
    }
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Magebay_Pdc::pdc_manage');
        $resultPage->getConfig()->getTitle()->prepend(__('Image Categories'));

        $resultPage->addBreadcrumb(__('Image Categories'), __('Image Categories'));
        $resultPage->addBreadcrumb(__('Image Categories'), __('Image Categories'));

        return $resultPage;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
