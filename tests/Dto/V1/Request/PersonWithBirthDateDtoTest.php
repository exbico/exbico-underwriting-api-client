<?php

namespace Exbico\Underwriting\Tests\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;
use Exbico\Underwriting\Dto\V1\Request\PersonWithBirthDateDto;
use Exception;
use PHPUnit\Framework\TestCase;

class PersonWithBirthDateDtoTest extends TestCase
{
    private const PERSON_FIRSTNAME   = 'Homer';
    private const PERSON_LASTNAME    = 'Simpson';
    private const PERSON_PATRONYMIC  = 'Petrovich';
    private const EXAMPLE_BIRTH_DATE = '1970-06-06';

    /**
     * @throws Exception
     */
    public function testCreateWithoutConstructor(): void
    {
        $person = new PersonWithBirthDateDto();
        $person->setFirstName(self::PERSON_FIRSTNAME);
        $person->setLastName(self::PERSON_LASTNAME);
        $person->setPatronymic(self::PERSON_PATRONYMIC);
        $person->setBirthDate(self::EXAMPLE_BIRTH_DATE);
        self::assertInstanceOf(AbstractDto::class, $person);
        self::assertEquals(self::PERSON_FIRSTNAME, $person->getFirstname());
        self::assertEquals(self::PERSON_LASTNAME, $person->getLastname());
        self::assertEquals(self::PERSON_PATRONYMIC, $person->getPatronymic());
        self::assertEquals(self::EXAMPLE_BIRTH_DATE, $person->getBirthDate());
    }

    public function testCreateViaConstructor(): void
    {
        $person = new PersonWithBirthDateDto(
            [
                'firstName'  => self::PERSON_FIRSTNAME,
                'lastName'   => self::PERSON_LASTNAME,
                'patronymic' => self::PERSON_PATRONYMIC,
                'birthDate'  => self::EXAMPLE_BIRTH_DATE,
            ]
        );
        self::assertInstanceOf(AbstractDto::class, $person);
        self::assertEquals(self::PERSON_FIRSTNAME, $person->getFirstName());
        self::assertEquals(self::PERSON_LASTNAME, $person->getLastName());
        self::assertEquals(self::PERSON_PATRONYMIC, $person->getPatronymic());
        self::assertEquals(self::EXAMPLE_BIRTH_DATE, $person->getBirthDate());
    }
}
