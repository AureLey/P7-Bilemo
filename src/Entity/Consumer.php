<?php

declare(strict_types=1);

/*
 * This file is part of Bilemo
 *
 * (c)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\ConsumerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConsumerRepository::class)]
class Consumer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getConsumers'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getConsumers'])]
    #[Assert\NotBlank(message: 'firstname is necessary')]
    #[Assert\Length(min: 6, max: 255, minMessage: 'First name must be a minimum of 6 for length', maxMessage: 'First name must be 255 maximum for length')]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getConsumers'])]
    #[Assert\NotBlank(message: 'Lastname is necessary')]
    #[Assert\Length(min: 6, max: 255, minMessage: 'Lastname must be a minimum of 6 for length', maxMessage: 'Lastname must be 255 maximum for length')]
    private ?string $lastname = null;

    #[ORM\ManyToOne(inversedBy: 'consumers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomerUser $user = null;

    /**
     * @var \DateTime
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'created', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt;

    /**
     * @var \DateTime
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name: 'updated', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUser(): ?CustomerUser
    {
        return $this->user;
    }

    public function setUser(?CustomerUser $user): self
    {
        $this->user = $user;

        return $this;
    }
}
