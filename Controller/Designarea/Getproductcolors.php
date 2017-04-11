<?php
namespace Magebay\Pdc\Controller\Designarea;
class Getproductcolors extends \Magento\Framework\App\Action\Action {
    protected $pdcSideModel;
    protected $productStatusModel;
    protected $pdpProductColorModel;
    protected $pdpProductColorImageModel;
    protected $pdcHelper;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebay\Pdc\Model\Pdpside $pdpside,
        \Magebay\Pdc\Model\Productstatus $productStatus,
        \Magebay\Pdc\Model\Pdpcolor $pdpColor,
        \Magebay\Pdc\Model\Pdpcolorimage $pdpColorImage,
        \Magebay\Pdc\Helper\Data $pdcHelper
    ) {
        $this->pdcSideModel = $pdpside;
        $this->productStatusModel = $productStatus;
        $this->pdpProductColorModel = $pdpColor;
        $this->pdpProductColorImageModel = $pdpColorImage;
        $this->pdcHelper = $pdcHelper;
        parent::__construct($context);
    }
    public function execute() {
        $response = array(
            'status' => 'error',
            'message' => 'Can not get product color. Something went worng!'
        );
        try {
            $responseData = array();
            $productId = $this->getRequest()->getParam("productid");
            $productColors = $this->pdpProductColorModel->getProductColorCollection($productId);
            if (!$productColors->count()) {
                $response = array(
                    'status' => 'error',
                    'message' => 'No item found. Please add product color'
                );
                echo json_encode($response);
                return false;
            }
            $_productDesignColor = $this->pdcHelper->getProductDesignColors($productId);
            if(!$this->pdcHelper->isProductColorTabEnable($_productDesignColor)) {
                $response = array(
                    'status' => 'error',
                    'message' => 'NOTE: This feature required all design sides must use background image and mask/overlay image. Please edit all side and try again.'
                );
                echo json_encode($response);
                return false;
            }
            if(isset($_productDesignColor['product_color_sides']) && !empty($_productDesignColor['product_color_sides'])) {
                //get side image for each color
                foreach($_productDesignColor['product_color_sides'] as $colorItem) {
                    $sidesImages = array();
                    $images = $this->pdpProductColorImageModel->getProductColorImage($colorItem['product_id'], $colorItem['id']);
                    foreach($images as $image) {
                        $sidesImages[]= $image->getData();
                    } 
                    $_productDesignColor['product_color_sides'][$colorItem['id']]['images'] = $sidesImages;
                }
            }
            $_productDesignColor['media_url'] = $this->pdcHelper->getMediaUrl() . "pdp/images/";
            $_productDesignColor['base_url'] = $this->pdcHelper->getBaseUrl();
            if(!empty($_productDesignColor)) {
                $response = array(
                    'status' => 'success',
                    'message' => 'Get product color successfully!',
                    'data' => $_productDesignColor
                );
            } 
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
    // //Return a array of all side & color
    // protected function getProductDesignColors($productId) {
    //     $sideModel = $this->pdcSideModel;
    //     $designSides = $sideModel->getDesignSides($productId);
    //     $defaultSideArr = array();
    //     foreach ($designSides as $side) {
    //         $defaultSideArr[$side->getId()] = $side->getData();
    //     }
    //     $productColorDataArr = array();
    //     $productColors = $this->pdpProductColorModel->getProductColorCollection($productId);
    //     foreach($productColors as $_productColor) {
    //         $productColorDataArr[$_productColor->getId()] = $_productColor->getData();
    //     }
    //     return array(
    //         'default_side' => $defaultSideArr,
    //         'product_color_sides' => $productColorDataArr
    //     );
    // }
    // //Mostly for product same as T-Shirt
    // protected function isProductColorTabEnable($productColors) {
    //     //Check all side use background image + mask image. 
    //     //Has product color item
    //     //Check default side using background and mask or not
    //     if(isset($productColors['default_side'])) {
    //         foreach($productColors['default_side'] as $_productSide) {
    //             if($_productSide["background_type"] != "image" || $_productSide["use_mask"] != 1) {
    //                 return false;
    //             }
    //         }
    //     }
    //     if(empty($productColors['product_color_sides'])) {
    //         return false;
    //     }
    //     return true;
    // }
}
