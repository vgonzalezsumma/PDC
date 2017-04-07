<?php
namespace Magebay\Pdc\Controller\Designarea;
class Templatelist extends \Magento\Framework\App\Action\Action {
    protected $adminTemplateModel;
    protected $pdcHelper;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebay\Pdc\Model\Admintemplate $adminTemplateModel,
        \Magebay\Pdc\Helper\Data $pdcHelper
    ) {
        $this->adminTemplateModel = $adminTemplateModel;
        $this->pdcHelper = $pdcHelper;
        parent::__construct($context);
    }
    public function execute() {
        $response = array(
            'status' => 'error',
            'message' => 'Can not get template list. Something went worng!',
            'media_url' => $this->pdcHelper->getMediaUrl() . "pdp/images/",
            'base_url' => $this->pdcHelper->getBaseUrl()
        );
        try {
            $responseData = array();
            $productId = $this->getRequest()->getParam("productid");
            $templates = $this->adminTemplateModel->getProductTemplates($productId);
            if (!$templates->count()) {
                $response['status'] = 'error';
                $response['message'] = 'There is no template found. Please create new template for this product';
                echo json_encode($response);
                return false;
            } else {
                $responseData['templates'] = array();
                foreach($templates as $template) {
                    $responseData['templates'][$template->getId()] = $template->getData();
                }
            }
            if(!empty($responseData)) {
                $response['status'] = 'success';
                $response['message'] = 'Get tempate list successfully!';
                $response['data'] = $responseData;
            } 
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
    //Return a array of all side & color
    protected function getProductDesignColors($productId) {
        $sideModel = $this->pdcSideModel;
        $designSides = $sideModel->getDesignSides($productId);
        $defaultSideArr = array();
        foreach ($designSides as $side) {
            $defaultSideArr[$side->getId()] = $side->getData();
        }
        $productColorDataArr = array();
        $productColors = $this->pdpProductColorModel->getProductColorCollection($productId);
        foreach($productColors as $_productColor) {
            $productColorDataArr[$_productColor->getId()] = $_productColor->getData();
        }
        return array(
            'default_side' => $defaultSideArr,
            'product_color_sides' => $productColorDataArr
        );
    }
    //Mostly for product same as T-Shirt
    protected function isProductColorTabEnable($productColors) {
        //Check all side use background image + mask image. 
        //Has product color item
        //Check default side using background and mask or not
        if(isset($productColors['default_side'])) {
            foreach($productColors['default_side'] as $_productSide) {
                if($_productSide["background_type"] != "image" || $_productSide["use_mask"] != 1) {
                    return false;
                }
            }
        }
        if(empty($productColors['product_color_sides'])) {
            return false;
        }
        return true;
    }
}
