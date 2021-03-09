<?php
declare(strict_types=1);

namespace Exbico\Api\V1\Dto\Request;

use Exbico\Api\AbstractDto;

final class PersonDto extends AbstractDto
{
    protected $firstname;
    protected $middlename;
    protected $lastname;

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    public function setMiddlename(string $middlename): void
    {
        $this->middlename = $middlename;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }
}