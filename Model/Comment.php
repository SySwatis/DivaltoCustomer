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

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;

class Comment
{
    /** @var LoggerInterface */
    private $_logger;

    /** @var OrderStatusHistoryRepositoryInterface */
    private $_orderStatusRepository;

    /** @var OrderRepositoryInterface */
    private $_orderRepository;

    public function __construct(
        OrderStatusHistoryRepositoryInterface $orderStatusRepository,
        OrderRepositoryInterface $orderRepository,
        PsrLoggerInterface $logger
    ) {
        $this->_orderStatusRepository = $orderStatusRepository;
        $this->_orderRepository = $orderRepository;
        $this->_logger = $logger;
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
            $order = $this->_orderRepository->get($orderId);
        } catch (NoSuchEntityException $exception) {
            $this->_logger->error($exception->getMessage());
        }
        $orderHistory = null;
        if ($order) {
            $comment = $order->addStatusHistoryComment(
                $commentText
            );
            try {
                $orderHistory = $this->_orderStatusRepository->save($comment);
            } catch (Exception $e) {
                $this->_logger->critical($e->getMessage());
            }
        }
        return $orderHistory;
    }

    public function getListCommentOrder ($orderId)
    {
        $order= $this->_orderRepository->get($orderId);
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