<?php
namespace Magebay\Pdc\Controller\Designarea;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Deletetemplate extends \Magento\Framework\App\Action\Action {
    protected $adminTemplateModel;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebay\Pdc\Model\Admintemplate $adminTemplateModel
    ) {
        $this->adminTemplateModel = $adminTemplateModel;
        parent::__construct($context);
    }
    public function execute() {
        $response = array(
            'status' => 'error',
            'message' => 'Can not delete this template. Something went wrong!'
        );
        try {
            $id = $this->getRequest()->getParam('id');
            if($id) {
                $result = $this->adminTemplateModel->load($id)->delete();
                if($result) {
                    $response = array(
                        'status' => 'success',
                        'message' => 'Template had deleted successfully!'
                    );
                } 
                  
            }
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
}
