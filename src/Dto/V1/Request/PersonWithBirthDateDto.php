<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Dto\V1\Request;

use Exbico\Underwriting\Dto\AbstractDto;

final class PersonWithBirthDateDto extends AbstractDto
{
    protected $firstName;
    protected $patronymic;
    protected $lastName;
    protected $birthDate;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function setPatronymic(string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    public function getLastname(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getBirthDate(): ?string
    {
        return $this->birthDate;
    }

    public function setBirthDate(string $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}