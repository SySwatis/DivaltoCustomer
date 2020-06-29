<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   Divalto
 * @package    Divalto_Customer
 * @subpackage Model
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */
 
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
    public function addCommentToOrder(int $orderId, $commentText)
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
                $commentText
            );
            try {
                $orderHistory = $this->orderStatusRepository->save($comment);
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }
        return $orderHistory;
    }

    public function getListCommentOrder ($orderId)
    {
        $order= $this->orderRepository->get($orderId);
        return $order->getStatusHistoryCollection();
    }

    public function getCommentDivalto($orderId,$value) 
    {
        $orderCommentCollection = $this->getListCommentOrder($orderId);
        $orderComment = [];
        foreach ( $orderCommentCollection as $status) {
            if ($status->getComment()) {
                $orderComment[] = $status->getComment();
            }
        }
        if($index = array_search($value, $orderComment))
        return $orderComment[$index]; // $key = 2;$orderComment;
    }

}