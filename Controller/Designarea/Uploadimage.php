<?php
namespace Magebay\Pdc\Controller\Designarea;
use Magebay\Pdc\Helper\Data as PdcHelper;
class Uploadimage extends \Magento\Framework\App\Action\Action {
    protected $pdcHelper;
    protected $uploaderFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PdcHelper $helper,
        \Magebay\Pdc\Model\UploadFactory $uploader
    ) {
        $this->pdcHelper = $helper;
        $this->uploaderFactory = $uploader;
        parent::__construct($context);
    }
    public function execute() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES["file"])) {
            $response = array(
                'status' => 'error',
                'message' => 'Can not upload image. Something went wrong!'
            );
            try {
                $imagePath = BP . "/pub/media/pdp/images/";
                $uploadResult = $this->uploaderFactory->create()->uploadFileAndGetName("file", $imagePath, array(), 'pdp/images/');
                if(isset($uploadResult['status']) && $uploadResult['status'] == "error") {
                    $this->getResponse()->setBody(json_encode($response));
                    exit();
                }
                if($uploadResult != "") {
                    //Create thumbnail
                    $basePath = $this->pdcHelper->getMediaBaseDir() . "images/" . $uploadResult;
                    $newPath = $this->pdcHelper->getMediaBaseDir() . "images/resize/";
                    $options = array(
                        'media-url' => $this->pdcHelper->getMediaUrl() . 'pdp/images/resize/'
                    );
                    $uploadThumbnail = ""; 
                    $thumbnailResult = $this->uploaderFactory->create()->resizeImage($basePath, $newPath, $options);
                    if($thumbnailResult) {
                        $uploadThumbnail = $thumbnailResult;
                    }
                    //End create thumbnail
                    $response = array(
                        'status' => 'success',
                        'message' => 'The image had successfully saved!',
                        'filename' => $uploadResult,
                        'thumbnail' => $uploadThumbnail
                    );
                }
            } catch(Exception $e) {
                
            }
            $this->getResponse()->setBody(json_encode($response));
		}
    }
}
