<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Tests\Unit;

use Exbico\Underwriting\Tests\Traits\WithClient;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    use WithClient;

    public function testClientInit(): void
    {
        self::assertNotNull($this->getClient()->getApiSettings());
        self::assertNotNull($this->getClient()->getHttpClient());
        self::assertNotNull($this->getClient()->getLogger());
        self::assertNotNull($this->getClient()->reports());
    }
}