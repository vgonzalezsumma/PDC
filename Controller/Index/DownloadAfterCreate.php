<?php
namespace Magebay\Pdc\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class DownloadAfterCreate extends \Magento\Framework\App\Action\Action
{
	protected $_urlBuilder;
	protected $_fileSystem;
	protected $_storeManager;
    public function __construct(
        Context $context,
		UrlInterface $urlBuilder,
		Filesystem $fileSystem,
		StoreManagerInterface $storeManager
    ) {
		$this->_urlBuilder = $urlBuilder;
		$this->_fileSystem = $fileSystem;
		$this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
		$mediaPath =  $this->_fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath();
		$fileName = $this->getRequest()->getParam('file-name','');
		$type = $this->getRequest()->getParam('type','');
		if($fileName == '' || $type == '')
		{
			return false;
		}
		$path = $mediaPath.'pdp/export/'.$type.'/';
		$fileDowload = $mediaPath.'pdp/export/'.$type.'/'.$fileName;
		if (file_exists($fileDowload)) {
			
			if($type == 'svg')
			{
				header('Content-Description: File Transfer');
				header('Content-Type: application/svg');
				header('Content-Disposition: attachment; filename='. $fileName);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileDowload));
				
			}
			else
			{
				
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($fileDowload));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fileDowload));
			}
			ob_clean();
			flush();
			readfile($fileDowload);
			exit;
		}  
    } 
}