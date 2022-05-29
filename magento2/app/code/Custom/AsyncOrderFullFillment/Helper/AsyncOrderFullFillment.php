<?php

/**
 * @author Syed Aun Abbas <syedaun.abbasrizvi@gmail.com>
 */

namespace Custom\AsyncOrderFullFillment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Custom\AsyncOrderFullFillment\Model\ERPTransmissionLogFactory;
use Custom\AsyncOrderFullFillment\Model\ResourceModel\ERPTransmissionLog\CollectionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;


class AsyncOrderFullFillment extends AbstractHelper
{

    const STATE_PROCESSING = 'processing';

    /** @var ERPTransmissionLogFactory */
    private $_erptransmissionLogFactory;

    /** @var CollectionFactory */
    private $_erptransmissionLogCollectionFactory;

    /** @var OrderInterface */
    private $_order;

    /** @var LoggerInterface */
    private $logger;


    /**
     * @param Context $context
     * @param ERPTransmissionLogFactory $erptransmissionLogFactory
     * @param CollectionFactory $erptransmissionLogCollectionFactory
     * @param OrderInterface $order
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ERPTransmissionLogFactory $erptransmissionLogFactory,
        CollectionFactory $erptransmissionLogCollectionFactory,
        OrderInterface  $order,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_erptransmissionLogFactory = $erptransmissionLogFactory;
        $this->_erptransmissionLogCollectionFactory = $erptransmissionLogCollectionFactory;
        $this->_order = $order;
        $this->logger = $logger;
    }



    /**
     * Store ERP Transmissions
     *
     * @param array $erpResponse
     * @return void
     */
    public function saveERPTransmissionLog(array $erpResponse)
    {
        try {
            $erptransmissionLog = $this->_erptransmissionLogFactory->create();

            $erptransmissionLog->setData([
                'order_id' => $erpResponse['order_id'],
                'code' => $erpResponse['response_code']
            ]);

            $erptransmissionLog->save();

            // Update Order status on successfull sync
            if ($erpResponse['response_code'] == 201)
                $this->updateOrderStatus($erpResponse['order_id']);
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }

    
    public function updateOrderStatus($orderId, $status = self::STATE_PROCESSING)
    {
        try {
            $orderData = $this->_order->load($orderId);
            $orderData->setState($status)->setStatus($status);

            $orderData->save();
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }


    /**
     * Fetch Trnasmission Collection
     */
    public function getTransmissionsCollection($isSuccess = 1, $limit = 10)
    {
        $erptransmissionLog = $this->_erptransmissionLogCollectionFactory->create();

        $addFilter = ['neq' => 400];

        if (!$isSuccess)
            $addFilter = ['neq' => 201];

        $erptransmissionLog->addFieldToFilter('code', $addFilter);
        return $erptransmissionLog->setPageSize($limit)->getItems();
    }
}
