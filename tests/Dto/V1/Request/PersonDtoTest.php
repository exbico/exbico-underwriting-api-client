<?php

namespace Exbico\Underwriting\Tests\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;
use Exbico\Underwriting\Dto\V1\Request\PersonDto;
use Exception;
use PHPUnit\Framework\TestCase;

class PersonDtoTest extends TestCase
{
    private const PERSON_FIRSTNAME = 'Homer';
    private const PERSON_LASTNAME = 'Simpson';
    private const PERSON_MIDDLENAME = 'Jay';

    /**
     * @throws Exception
     */
    public function testCreateWithoutConstructor(): void
    {
        $person = new PersonDto();
        $person->setFirstname(self::PERSON_FIRSTNAME);
        $person->setLastname(self::PERSON_LASTNAME);
        $person->setMiddlename(self::PERSON_MIDDLENAME);
        self::assertInstanceOf(AbstractDto::class, $person);
        self::assertEquals(self::PERSON_FIRSTNAME, $person->getFirstname());
        self::assertEquals(self::PERSON_LASTNAME, $person->getLastname());
        self::assertEquals(self::PERSON_MIDDLENAME, $person->getMiddlename());
    }

    public function testCreateViaConstructor(): void
    {
        $person = new PersonDto([
            'firstname' => self::PERSON_FIRSTNAME,
            'lastname' => self::PERSON_LASTNAME,
            'middlename' => self::PERSON_MIDDLENAME,
        ]);
        self::assertInstanceOf(AbstractDto::class, $person);
        self::assertEquals(self::PERSON_FIRSTNAME, $person->getFirstname());
        self::assertEquals(self::PERSON_LASTNAME, $person->getLastname());
        self::assertEquals(self::PERSON_MIDDLENAME, $person->getMiddlename());
    }
}
