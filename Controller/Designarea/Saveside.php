<?php
namespace Magebay\Pdc\Controller\Designarea;
class Saveside extends \Magento\Framework\App\Action\Action {
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
            'message' => 'Can not save side. Something went wrong!'
        );
        try {
            $postData = file_get_contents("php://input");
            $dataDecoded = json_decode($postData, true);
            $result = $this->pdcSideModel->saveProductSide($dataDecoded);
            if($result) {
                 $responseData['sides'] = array();
                $sides = $this->pdcSideModel->getDesignSides($dataDecoded['product_id']);
                foreach($sides as $side) {
                    $responseData['sides'][$side->getId()] = $side->getData();
                        
                }
                $response = array(
                    'status' => 'success',
                    'message' => 'Side saved successfully!',
                    'data' => array(
                        'sides' => $responseData['sides']
                    )
                );
            } 
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
}
