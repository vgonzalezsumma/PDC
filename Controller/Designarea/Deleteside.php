<?php
namespace Magebay\Pdc\Controller\Designarea;
class Deleteside extends \Magento\Framework\App\Action\Action {
    protected $pdcSideModel;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebay\Pdc\Model\Pdpside $pdpside
    ) {
        $this->pdcSideModel = $pdpside;
        parent::__construct($context);
    }
    public function execute() {
        $response = array(
            'status' => 'error',
            'message' => 'Can not delete side. Something went wrong!'
        );
        try {
            $id = $this->getRequest()->getParam('id');
            $productId = $this->getRequest()->getParam('product-id');
            if($id) {
                $result = $this->pdcSideModel->load($id)->delete();
                if($result) {
                    $responseData['sides'] = array();
                    $sides = $this->pdcSideModel->getDesignSides($productId);
                    foreach($sides as $side) {
                        $responseData['sides'][$side->getId()] = $side->getData();
                            
                    }
                    $response = array(
                        'status' => 'success',
                        'message' => 'Side deleted successfully!',
                        'data' => array(
                            'sides' => $responseData['sides']
                        )
                    );
                } 
                  
            }
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
}
