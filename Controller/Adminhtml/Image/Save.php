<?php
namespace Magebay\Pdc\Controller\Adminhtml\Image;
class Save extends \Magebay\Pdc\Controller\Adminhtml\Image
{
    protected $resultPageFactory;
    protected $resultForwardFactory;
    protected $resultRedirectFactory;
    protected $imageFactory;
    //Upload image field
    protected $adapterFactory;
    protected $uploader;
    protected $pdcHelper;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magebay\Pdc\Model\ImageFactory $_imageFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magebay\Pdc\Model\Upload $uploader,
        \Magebay\Pdc\Helper\Data $pdcHelper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->imageFactory = $_imageFactory;
        $this->adapterFactory = $adapterFactory;
        $this->uploader = $uploader;
        $this->pdcHelper = $pdcHelper;
        parent::__construct($context, $coreRegistry);
    }
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        //\Zend_Debug::dump($data);die;
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            //\Zend_Debug::dump($id);
            $model = $this->imageFactory->create()->load($id);
            //\Zend_Debug::dump($this->_objectManager->create('Magebay\Pdc\Model\Image'));
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This image no longer exists.'));
                
                return $resultRedirect->setPath('*/*/');
            }
            // start block upload image
            $imageFields = array("filename", "thumbnail");
            foreach($imageFields as $imageFieldName) {
                $imagePath = BP . "/pub/media/pdp/images/artworks/";
                $uploadResult = $this->uploader->uploadFileAndGetName($imageFieldName, $imagePath, $data, 'pdp/images/artworks/');
                if(isset($uploadResult['status']) && $uploadResult['status'] == "error") {
                    $errorMessage = 'Something went wrong. Can not upload image to the server';
                    if(isset($uploadResult['message'])) {
                        $errorMesasge = $uploadResult['message'];
                        // display error message
                        $this->messageManager->addError($errorMesasge);
                        // save data in session
                        $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                        // redirect to edit form
                        return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                    }
                }
                $data[$imageFieldName] = $uploadResult;   
            }
            if($data['filename'] == "") {
                // display error message
                $this->messageManager->addError("Please choose image to upload");
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
            // end block upload image
            //Auto create thumbnail if thumbnail field is empty
            if($data['filename'] != '' && $data['thumbnail'] == '') {
                //Create thumbnail
                $basePath = $this->pdcHelper->getMediaBaseDir() . "images/artworks/" . $data['filename'];
                $newPath = $this->pdcHelper->getMediaBaseDir() . "images/artworks/resize/";
                $options = array(
                    'media-url' => $this->pdcHelper->getMediaUrl() . 'pdp/images/artworks/resize/'
                );
                $uploadThumbnail = ""; 
                $thumbnailResult = $this->uploader->resizeImage($basePath, $newPath, $options);
                if($thumbnailResult) {
                    $thumbnailFilename = explode('/', $thumbnailResult);
                    $data['thumbnail'] = 'resize/' . end($thumbnailFilename);
                }
                //End create thumbnail
            }
            // init model and set data

            $model->setData($data);
	         
            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                $this->messageManager->addSuccess(__('Image had been saved successfully!'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
