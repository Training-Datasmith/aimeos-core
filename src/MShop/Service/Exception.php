<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Service
 */
namespace Aimeos\M_Shop\Service;

/**
 * \Exception thrown by service objects.
 *
 * @package MShop
 * @subpackage Service
 */
class Exception extends \Aimeos\M_Shop\Exception
{
    /**
     * Initializes the exception object.
     *
     * @param string $message The exception message to throw
     * @param int $code The exception code
     * @param \Exception|null $previous The previous exception used for the exception chaining
     * @param array $errorCodes Associative list of error codes
     */
    public function __construct($message = '', $code = 0, $previous = null, private array $error_codes = [])
    {
        parent::__construct($message, $code);
    }
    /**
     * Returns the error codes stored by the exception.
     *
     * @return array Associative list of error codes
     */
    public function get_error_codes(): array
    {
        return $this->error_codes;
    }
}