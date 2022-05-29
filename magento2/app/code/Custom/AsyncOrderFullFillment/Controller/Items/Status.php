<?php

/**
 * @author Syed Aun Abbas <syedaun.abbasrizvi@gmail.com>
 */

namespace Custom\AsyncOrderFullFillment\Controller\Items;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Custom\AsyncOrderFullFillment\Helper\AsyncOrderFullFillment;

class Status extends Action
{

    /** @var AsyncOrderFullFillment */
    private $_helper;

    public function __construct(
        Context $context,
        AsyncOrderFullFillment $helper
    ) {
        $this->_helper = $helper;
        return parent::__construct($context);
    }

    public function execute()
    {
        $transmissionCollection = $this->_helper->getTransmissionsCollection();

        echo "<pre>";
        foreach ($transmissionCollection as $item) {
            print_r($item->getData());
        }
    }
}
