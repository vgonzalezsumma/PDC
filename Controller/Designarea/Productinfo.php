<?php
namespace Magebay\Pdc\Controller\Designarea;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Productinfo extends \Magento\Framework\App\Action\Action {
    protected $pdcSideModel;
    protected $productStatusModel;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebay\Pdc\Model\Pdpside $pdpside,
        \Magebay\Pdc\Model\Productstatus $productStatus
    ) {
        $this->pdcSideModel = $pdpside;
        $this->productStatusModel = $productStatus;
        parent::__construct($context);
    }
    public function execute() {
        $response = array(
            'status' => 'error',
            'message' => 'Can not get product info. Something went worng!'
        );
        try {
            $responseData = array();
            $productId = $this->getRequest()->getParam("productid");
            //Get product sides
            $responseData['sides'] = array();
            $sides = $this->pdcSideModel->getDesignSides($productId);
            foreach($sides as $side) {
                $responseData['sides'][$side->getId()] = $side->getData();
                    
            }
            //Get product status - config
            $responseData['productinfo'] = array(
                'status' => "2",
                'product_id' => $productId,
                'note' => array(
				    'show_price' => "2",
				    'text_price' => "0",
				    'clipart_price' => "0",
                    'default_color' => '#000',
                    'default_fontsize' => "25",
                    'default_fontheight' => "1",
                    'auto_replace_pattern' => "2",
                    'enable_upload_plugin' => "1",
                    'enable_clipart_plugin' => "1",
                    'enable_background_plugin' => "1",
                    'enable_shape_plugin' => "1",
                    'enable_frame_plugin' => "2",
                    'enable_facebook_plugin' => "2",
                    'enable_instagram_plugin' => "2",
                    'enable_qrcode_plugin' => "2",
                    'enable_colorpicker_plugin' => "1",
                    'enable_curvedtext_plugin' => "2",
                    'enable_image_plugin' => "1",
                    'enable_product_design_tab' => "1",
                    'enable_elements_tab' => "1",
                    'enable_upload_tab' => "1",
                    'enable_text_tab' => "1",
                    'enable_layer_tab' => "1",
                    'enable_info_tab' => "2",
                    'enable_download_btn' => "1",
                    'enable_share_btn' => "2",
                    'enable_reset_btn' => "1",
                    'enable_zoom_btn' => "1"
                )
            );
            $_productInfoFromDb = $this->productStatusModel->getProductConfig($productId);
            if(isset($_productInfoFromDb['id'])) {
                $responseData['productinfo'] = $_productInfoFromDb;    
            }
            if(!empty($responseData)) {
                $response = array(
                    'status' => 'success',
                    'message' => 'Get product info successfully!',
                    'data' => $responseData
                );
            } 
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
}
