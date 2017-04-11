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
            'message' => 'Can not save side. Something went worng!'
        );
        try {
            $postData = file_get_contents("php://input");
            $dataDecoded = json_decode($postData, true);
            $result = $this->pdcSideModel->saveProductSide($dataDecoded);
            if($result) {
                $response = array(
                    'status' => 'success',
                    'message' => 'Side saved successfully!',
                    'data' => $result
                );
            } 
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
}
