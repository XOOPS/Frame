<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 1/13/18
 * Time: 10:05 AM
 */

namespace Xoops\Frame\Exception;

/**
 * RackExhaustedException - attempt to advance past the end of the middleware queue
 *
 * Possible causes
 * - No response message has been returned and there is no handler on the middleware queue to delegate to
 *
 * @category  Xoops\Xadr\Exception
 * @package   Xadr
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2017 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class RackExhaustedException extends \RuntimeException
{
}
