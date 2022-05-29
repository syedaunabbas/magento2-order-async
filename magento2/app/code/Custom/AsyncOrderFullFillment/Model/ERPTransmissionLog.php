<?php

/**
 *  @author Syed Aun Abbas <syedaun.abbasrizvi@email.com>
 */

namespace Custom\AsyncOrderFullFillment\Model;

class ERPTransmissionLog extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'custom_asyncorderfullfillment_erpransmissionLog'; // unique identifier for use within caching

    protected $_eventPrefix = 'custom_asyncorderfullfillment_erpransmissionLog'; // when access in event

    
    protected function _construct()
    {
        $this->_init('Custom\AsyncOrderFullFillment\Model\ResourceModel\ERPTransmissionLog');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
