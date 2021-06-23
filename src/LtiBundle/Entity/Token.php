<?php
/* For licensing terms, see /license.txt */

declare(strict_types=1);

namespace Chamilo\LtiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Token.
 *
 * @package Chamilo\LtiBundle\Entity
 *
 * @ORM\Table(name="lti_token")
 * @ORM\Entity
 */
class Token
{
    const TOKEN_LIFETIME = 3600;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     */
    protected int $id;
    /**
     * @var ExternalTool
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\LtiBundle\Entity\ExternalTool")
     * @ORM\JoinColumn(name="tool_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ExternalTool $tool;
    /**
     * @var array
     *
     * @ORM\Column(name="scope", type="json")
     */
    private array $scope;
    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string")
     */
    private string $hash;
    /**
     * @var int
     *
     * @ORM\Column(name="created_at", type="integer")
     */
    private int $createdAt;
    /**
     * @var int
     *
     * @ORM\Column(name="expires_at", type="integer")
     */
    private int $expiresAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getTool(): ExternalTool
    {
        return $this->tool;
    }

    public function setTool(ExternalTool $tool): static
    {
        $this->tool = $tool;

        return $this;
    }

    /**
     * @return array
     */
    public function getScope(): array
    {
        return $this->scope;
    }

    public function setScope(array $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(int $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getScopeInString(): string
    {
        return implode(' ', $this->scope);
    }

    public function generateHash(): static
    {
        $this->hash = sha1(uniqid((string) mt_rand()));

        return $this;
    }
}
