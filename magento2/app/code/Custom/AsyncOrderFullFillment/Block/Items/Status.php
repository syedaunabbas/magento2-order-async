<?php

/**
 * @author Syed Aun Abbas <syedaun.abbasrizvi@gmail.com>
 */

namespace Custom\AsyncOrderFullFillment\Block\Items;

use Custom\AsyncOrderFullFillment\Helper\AsyncOrderFullFillment;

class Status extends \Magento\Framework\View\Element\Template
{

    /** @var AsyncOrderFullFillment */
    private $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        AsyncOrderFullFillment $helper,
        array $data
    ) {

        #die('222');
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function showTransmissionCollection()
    {
        return $this->_helper->getTransmissionsCollection();
    }
}
