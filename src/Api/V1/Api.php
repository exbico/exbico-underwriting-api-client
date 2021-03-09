<?php
declare(strict_types=1);

namespace Exbico\Api\V1;

use Exbico\Api\AbstractApi;

abstract class Api extends AbstractApi
{
    private const API_VERSION = 'v1';

    public function getApiVersion(): string
    {
        return self::API_VERSION;
    }
}