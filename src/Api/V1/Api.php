<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Api\V1;

use Exbico\Underwriting\Api\AbstractApi;

abstract class Api extends AbstractApi
{
    private const API_VERSION = 'v1';

    public function getApiVersion(): string
    {
        return self::API_VERSION;
    }
}