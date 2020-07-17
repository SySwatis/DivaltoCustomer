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
 * @subpackage Logger
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */
namespace Divalto\Customer\Logger\Handler;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger as MonologLogger;

class Error extends BaseHandler
{
    
	 /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = MonologLogger::ERROR;

	/**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/divalto/customer/error.log';
}