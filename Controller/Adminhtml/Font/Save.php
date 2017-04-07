<?php
namespace Magebay\Pdc\Controller\Adminhtml\Font;
class Save extends \Magebay\Pdc\Controller\Adminhtml\Font
{
    protected $resultPageFactory;
    protected $resultForwardFactory;
    protected $resultRedirectFactory;
    protected $fontsModel;
    //Upload image field
    protected $adapterFactory;
    protected $pdcHelper;
    protected $uploaderFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magebay\Pdc\Model\Fonts $fontsModel,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magebay\Pdc\Helper\Data $pdcHelper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->fontsModel = $fontsModel;
        $this->pdcHelper = $pdcHelper;
        $this->uploaderFactory = $uploaderFactory;
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
			if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try
                {
                    $path = $this->pdcHelper->getMediaBaseDir() . "fonts";
                    $fname = $_FILES['filename']['name']; //file name
					$data['original_filename'] = $fname;
                    if(isset($data['display_text']) && $data['display_text'] == '') {
                        $tempFontName = explode('.', $data['original_filename']);
                        $data['display_text'] = $tempFontName[0];
                    }
                    $ext = substr($fname , strrpos($fname , '.') + 1);
					$fname = str_replace(" ", "_", strtolower($fname));
                    $fnameArr = explode('.', $fname);
                    $data['ext'] = $fnameArr[1];
                    $data['name'] = $fnameArr[0];
                    //$fullname = $path.$fname;
                    $uploader = $this->uploaderFactory->create(['fileId' => "filename"]);
                    $allowFonts = array("ttf", "otf", "fnt", "fon", "woff", "dfont");
                    $uploader->setAllowedExtensions($allowFonts); //Allowed extension for file
                    $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $uploader->save($path, $fname); 
                }
                catch (Exception $e)
                {
					// display error message
                    $this->messageManager->addError($e->getMessage());
                    // save data in session
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                    // redirect to edit form
                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('font_id')]);
                }
            }
            $model = $this->fontsModel;
			$currentId = $this->getRequest()->getParam('font_id');
			if($currentId > 0 && !isset($data['ext']))
			{
				$oldData = $model->load($currentId)->getData();
				$data['name'] = $oldData['name'];
				$data['ext'] = $oldData['ext'];
				$data['original_filename'] = $oldData['original_filename'];
				if(isset($data['display_text']) && $data['display_text'] == '') {
                    $data['display_text'] = $data['name'];
                }
			}
            if(isset($data['name']) && $data['name'] != "") {
                $model->setData($data)->setId($this->getRequest()->getParam('font_id'));    
            } else {                
                $this->messageManager->addError("Please choose font to upload.");
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('font_id')]);
            }
            try {
                // save the data
                $model->save();
                // display success message
                $this->messageManager->addSuccess(__('Font had been saved successfully!'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('font_id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
