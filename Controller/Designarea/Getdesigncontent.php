<?php
namespace Magebay\Pdc\Controller\Designarea;
class Getdesigncontent extends \Magento\Framework\App\Action\Action {
    protected $pdcHelper;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebay\Pdc\Helper\Data $pdcHelper
    ) {
        $this->pdcHelper = $pdcHelper;
        parent::__construct($context);
    }
    public function execute() {
        $params = $this->getRequest()->getPostValue();
        if(isset($params['json_filename']) && $params['json_filename']) {
            $designContent = $this->pdcHelper->getPDPJsonContent($params['json_filename']);
            if($designContent) {
                echo $designContent;
            }
        }
    }
}
