<?php 
namespace Magebay\Pdc\Model;
class Upload {
    protected $adapterFactory;
    protected $uploaderFactory;
    protected $storeManager;
    //This string might contain in image url
    protected $imagePrefix = 'pdp/images';
    protected $supportImages = array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg+xml', 'svg');
    public function __construct(
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploader;
        $this->storeManager = $storeManager;
    }
    public function uploadFileAndGetName($input, $destinationFolder, $data, $prefix = 'pdp/images/') {
        try {
            if (isset($data[$input]['delete'])) {
                return '';
            } else {
                $uploader = $this->uploaderFactory->create(['fileId' => $input]);
                $uploader->setAllowedExtensions($this->supportImages);
                $imageAdapter = $this->adapterFactory->create();
                $uploader->addValidateCallback($input, $imageAdapter, 'validateUploadFile');
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $uploader->setAllowCreateFolders(true);
                //Set custom name for upload file
                $imageName = $_FILES[$input]['name'];
                $ext = substr($imageName, strrpos($imageName, '.') + 1);
                $filename = $input . time() . '.' . $ext;
                $validFilename1 = str_replace('_', '', $filename);
                $validFilename2 = str_replace('-', '', $validFilename1);
                $filename = $validFilename2;
                $result = $uploader->save($destinationFolder, $filename);
                if(isset($result['file'])) {
                    return $result['file'];
                }
            }
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                //die('Something went wrong. Can not upload image to server');
                return array(
                    'status' => 'error',
                    'message' => $e->getMessage()
                );
            } else {
                if (isset($data[$input]['value'])) {
                    //Should replace prefix with empty string, edit mode
                    $finalFilename = str_replace($prefix, '', $data[$input]['value']);
                    return $finalFilename;
                }
            }
        }
        return '';
    }
    /**
    $options[width]
    $options[height]
    $options[media-url]
    **/
    public function resizeImage($basePath, $newPath = '', $options = array()) {
        if(!file_exists($basePath)) {
            return false;
        }
        $extTemp = explode(".", $basePath);
        if(end($extTemp) == "svg") {
            return false;
        }
        //Image name 
        $width = 150;
        $height = 150;
        if(isset($options['width'])) {
            $width = $options['width'];
        }
        if(isset($options['height'])) {
            $height = $options['height'];
        }
        $nameTemp = explode('/', $basePath);
        $newFilename = "resize_" . end($nameTemp);
        //Create new folder if not exists
        if(!file_exists($newPath)) {
            mkdir($newPath, 0777, true);
            if(!file_exists($newPath)) {
                return false;
            }
        }
        
        $imageResize = $this->adapterFactory->create();
        $imageResize->open($basePath);
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame(FALSE);
        $imageResize->keepAspectRatio(true);
        $imageResize->backgroundColor(array(255,255,255));
        $imageResize->resize($width, $height);
        
        $imageResize->save($newPath . $newFilename);
        if(file_exists($newPath . $newFilename)) {
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $this->imagePrefix;
            if(isset($options['media-url'])) {
                $mediaUrl = $options['media-url'];
            }
            return $mediaUrl . $newFilename;
        }
        return false;
    }
}