<?php

/**
 *  @author Syed Aun Abbas <syedaun.abbasrizvi@email.com>
 */

namespace Custom\AsyncOrderFullFillment\Model\ResourceModel\ERPTransmissionLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\AsyncOrderFullFillment\Model\ERPTransmissionLog', 'Custom\AsyncOrderFullFillment\Model\ResourceModel\ERPTransmissionLog');
    }
}
