<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 1/13/18
 * Time: 9:50 AM
 */

namespace Xoops\Frame\Exception;

/**
 * InvalidHandlerException - attempt to queue an invalid handler to the Rack
 *
 * Possible causes
 * - Trying to register an instance of Rack as its own middleware
 *
 * @category  Xoops\Xadr\Exception
 * @package   Xadr
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2017 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class InvalidHandlerException extends \InvalidArgumentException
{
}
