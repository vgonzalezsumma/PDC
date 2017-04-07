<?php
namespace Magebay\Pdc\Controller\Designarea;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Saveproductcolor extends \Magento\Framework\App\Action\Action {
    protected $pdpProductColorModel;
    protected $pdpProductColorImageModel;
    protected $uploadModel;
    protected $pdcHelper;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebay\Pdc\Model\Pdpcolor $pdpColor,
        \Magebay\Pdc\Model\Pdpcolorimage $pdpColorImage,
        \Magebay\Pdc\Model\Upload $upload,
        \Magebay\Pdc\Helper\Data $helper
    ) {
        $this->pdpProductColorModel = $pdpColor;
        $this->pdpProductColorImageModel = $pdpColorImage;
        $this->uploadModel = $upload;
        $this->pdcHelper = $helper;
        parent::__construct($context);
    }
    public function execute() {
        $response = array(
            'status' => 'error',
            'message' => 'Can not save product color. Something went wrong!'
        );
        try {
            $postData = file_get_contents("php://input");
            $dataDecoded = json_decode($postData, true);
            $productColorId = $this->pdpProductColorModel->saveProductColor($dataDecoded);
            if($productColorId) {
                $productColorImageInfo['product_color_id'] = $productColorId;
                foreach ($dataDecoded['design_sides'] as $sideId) {
					$productColorImageInfo['side_id'] = $sideId;
					$filename = $dataDecoded['color_image_' . $sideId];
                    $overlayFilename = $dataDecoded['overlay_image_' . $sideId];
                    if($filename != "" && $overlayFilename != "") {
                        $productColorImageInfo['filename'] = $filename;
                        //Create thumbnail
                        $thumbnail = $this->pdcHelper->getMediaBaseDir() . 'images/resize/' . $filename;
                        if(file_exists($thumbnail)) {
                            $productColorImageInfo['filename_thumbnail'] = 'resize/resize_' . filename;
                        }
                        //End create thumbnail
                        $productColorImageInfo['overlay'] = $overlayFilename;
				        $this->pdpProductColorImageModel->saveProductColorImage($productColorImageInfo);
                    }
				}
                $response = array(
                    'status' => 'success',
                    'message' => 'Side saved successfully!',
                );
            } 
        } catch(Exception $error) {
              
        }
        echo json_encode($response);
    }
}
