<?php
namespace Magebay\Pdc\Controller\Designarea;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Deleteproductcolor extends \Magento\Framework\App\Action\Action {
    protected $pdpColorModel;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebay\Pdc\Model\Pdpcolor $pdpColor
    ) {
        $this->pdpColorModel = $pdpColor;
        parent::__construct($context);
    }
    public function execute() {
        $response = array(
            'status' => 'error',
            'message' => 'Can not delete this product color. Something went wrong!'
        );
        try {
            $id = $this->getRequest()->getParam('id');
            if($id) {
                $result = $this->pdpColorModel->deleteProductColor($id);
                if($result) {
                    $response = array(
                        'status' => 'success',
                        'message' => 'Product color deleted successfully!'
                    );
                } 
                  
            }
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
}
