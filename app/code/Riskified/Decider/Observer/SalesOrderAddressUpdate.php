<?php
namespace Riskified\Decider\Observer;

use Magento\Framework\Event\ObserverInterface;
use Riskified\Decider\Api\Api;

class SalesOrderAddressUpdate implements ObserverInterface
{
    private $logger;
    private $apiOrder;
    private $orderFactory;

    public function __construct(
        \Riskified\Decider\Logger\Order $logger,
        \Riskified\Decider\Api\Order $orderApi,
        \Magento\Sales\Model\Order $orderFactory
    ) {
        $this->logger = $logger;
        $this->apiOrder = $orderApi;
        $this->orderFactory = $orderFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order_id = $observer->getOrderId();
            $order = $this->orderFactory->load($order_id);

            if (!$order) {
                return;
            }

            $this->apiOrder->post($order, Api::ACTION_UPDATE);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
