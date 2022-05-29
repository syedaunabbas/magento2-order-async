<?php

/**
 *  @author Syed Aun Abbas <syedaun.abbasrizvi@gmail.com>
 */

namespace Custom\AsyncOrderFullFillment\Model\Queue;

use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Custom\AsyncOrderFullFillment\Service\ERPMockApiService;
use Custom\AsyncOrderFullFillment\Helper\AsyncOrderFullFillment;

/**
 * Class Consumer
 */
class Consumer
{

    const STATE_PROCESSING = 'processing';

    /** @var LoggerInterface */
    private $logger;

    /** @var Json */
    private $_json;

    /** @var ERPMockApiService */
    private $_erpMockApi;

    /** @var AsyncOrderFullFillment */
    private $_helper;



    /**
     * @param Json $json
     * @param AsyncOrderFullFillment $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Json $json,
        ERPMockApiService $erpMockApi,
        AsyncOrderFullFillment $helper,
        LoggerInterface $logger
    ) {
        $this->_json = $json;
        $this->_erpMockApi = $erpMockApi;
        $this->_helper = $helper;
        $this->logger = $logger;
    }

    /**
     * @param string $orderInfo
     * @return void
     */
    public function processOrder(string $orderInfo): void
    {
        try {
            // Unserialize consumed data
            $data = $this->_json->unserialize($orderInfo);

            $response = $this->_erpMockApi->store($data);

            $mutatedResp = [
                'order_id' => $data['order_id'],
                'response_code' => $response->getStatusCode()
            ];

            $this->_helper->saveERPTransmissionLog($mutatedResp);

            $this->logger->info(__METHOD__ . ':: API Response response code::' . $response->getStatusCode());

            $this->logger->info(__METHOD__ . ':: API Response response body Methods::' . $this->_json->serialize(get_class_methods($response->getBody())));
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }
}
