<?php
namespace Magebay\Pdc\Block\Adminhtml\Font\Grid\Renderer;
use Magento\Store\Model\StoreManagerInterface;
class Fontpreview extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $storeManager;
    public function __construct(
        \Magento\Backend\Block\Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        //\Zend_Debug::dump($row->getData());
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);        
        $displayText = $row->getDisplayText();
        if($displayText == "") {
            $displayText = $row->getName();
        }
		$out = '<style type="text/css">';
		$out .= '@font-face {';
			$out .= 'font-family: "'. $row->getName() .'"';
			$fontPath = $mediaUrl . 'pdp/fonts/' . $row->getName() . '.' . $row->getExt();
			$out .= ';src: url("' . $fontPath .'")';
		
		$out .= '}';
		$out .= '</style>'; 
        $out .= '<span style="font-family: '. $row->getName() .'">'. $displayText .'</span>';
        return $out;
        
    }
}
