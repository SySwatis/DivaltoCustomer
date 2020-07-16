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

use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
   
class Order
{ 
    protected $orderRepository;
  
    protected $searchCriteriaBuilder;
    
    public function __construct(
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }
    
    public function getOrderById($id) 
    {
        return $this->orderRepository->get($id);
    }
    
    public function getOrderByIncrementId($incrementId) 
    {
        
        $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId);
  
        $order = $this->orderRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems(); // FirstItem ???
  
        return $order;
    }

}