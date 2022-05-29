<?php

/**
 *  @author Syed Aun Abbas <syedaun.abbasrizvi@email.com>
 */

namespace Custom\AsyncOrderFullFillment\Model\ResourceModel;

class ERPTransmissionLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('erp_transmission_log', 'id'); // table name , primary key
    }
}
