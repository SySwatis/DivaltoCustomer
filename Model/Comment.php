<?php
namespace Divalto\Customer\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Psr\Log\LoggerInterface;

class Comment
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OrderStatusHistoryRepositoryInterface */
    private $orderStatusRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        OrderStatusHistoryRepositoryInterface $orderStatusRepository,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * add comment to the order history
     *
     * @param int $orderId
     * @return OrderStatusHistoryInterface|null
     */
    public function addCommentToOrder(int $orderId)
    {
        $order = null;
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $exception) {
            $this->logger->error($exception->getMessage());
        }
        $orderHistory = null;
        if ($order) {
            $comment = $order->addStatusHistoryComment(
                'Comment for the order'
            );
            try {
                $orderHistory = $this->orderStatusRepository->save($comment);
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }
        return $orderHistory;
    }
}