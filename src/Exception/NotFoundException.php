<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Exception;

use Throwable;

class NotFoundException extends HttpException
{
    public const HTTP_STATUS = 404;

    public function __construct($message = '', $code = self::HTTP_STATUS, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
