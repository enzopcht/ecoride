<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class DriverPreference
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\Field(type: 'string')]
    private string $userId;

    #[MongoDB\Field(type: 'bool')]
    private bool $musicAllowed = true;

    #[MongoDB\Field(type: 'bool')]
    private bool $smokingAllowed = false;

    #[MongoDB\Field(type: 'bool')]
    private bool $animalsAllowed = false;

    #[MongoDB\Field(type: 'collection')]
    private array $customPreferences = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getMusicAllowed(): bool
    {
        return $this->musicAllowed;
    }

    public function setMusicAllowed(bool $musicAllowed): self
    {
        $this->musicAllowed = $musicAllowed;
        return $this;
    }

    public function getSmokingAllowed(): bool
    {
        return $this->smokingAllowed;
    }

    public function setSmokingAllowed(bool $smokingAllowed): self
    {
        $this->smokingAllowed = $smokingAllowed;
        return $this;
    }

    public function getAnimalsAllowed(): bool
    {
        return $this->animalsAllowed;
    }

    public function setAnimalsAllowed(bool $animalsAllowed): self
    {
        $this->animalsAllowed = $animalsAllowed;
        return $this;
    }

    public function getCustomPreferences(): array
    {
        return $this->customPreferences;
    }

    public function setCustomPreferences(array $customPreferences): self
    {
        $this->customPreferences = $customPreferences;
        return $this;
    }

    public function addCustomPreference(string $preference): self
    {
        if (!in_array($preference, $this->customPreferences)) {
            $this->customPreferences[] = $preference;
        }
        return $this;
    }

    public function removeCustomPreference(string $preference): self
    {
        $this->customPreferences = array_filter($this->customPreferences, fn($p) => $p !== $preference);
        return $this;
    }
}
