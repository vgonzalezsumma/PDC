<?php 
namespace Magebay\Pdc\Block;
use Magebay\Pdc\Helper\Data as PdcHelper;
class Angularjs extends \Magento\Framework\View\Element\Template {
    public $assetRepository;
    public $pdcHelper;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        PdcHelper $pdcHelper,
        array $data = []
    )
    {
        $this->assetRepository = $context->getAssetRepository();
        $this->pdcHelper = $pdcHelper;
        return parent::__construct($context, $data);
    } 
}