<?php

/**
 *  @author Syed Aun Abbas <syedaun.abbasrizvi@gmail.com>
 */

namespace Custom\AsyncOrderFullFillment\Plugin;

use Psr\Log\LoggerInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;

/**
 * Class OrderManagement
 */
class OrderManagement
{

    /** @var PublisherInterface */
    private $publisher;

    /** @var LoggerInterface */
    private $logger;

    /** @var Json */
    private $_json;

    const TOPIC_NAME = 'custom.order.create';


    /**
     * @param PublisherInterface $publisher
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        PublisherInterface $publisher,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->publisher = $publisher;
        $this->_json = $json;
        $this->logger = $logger;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     */
    public function afterPlace(OrderManagementInterface $subject, OrderInterface $result): OrderInterface
    {
        try {
            // Set data to queue
            $data = [
                'order_id' => $result->getEntityId(),
                'increment_id' => $result->getIncrementId(),
                'customer_email' => $result->getCustomerEmail(),
                'order_grand_total' => $result->getGrandTotal(),
                'order_sub_total' => $result->getSubtotal()
            ];

            $this->publisher->publish(self::TOPIC_NAME, $this->_json->serialize($data));
            $this->logger->info(__METHOD__ . '::' . $this->_json->serialize($data));
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $result;
    }
}
