<?php

namespace App\Entity;

use ApiPlatform\Metadata\Link;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[
    ApiResource(
    normalizationContext: ['groups' => ['purchase:read']],
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/users/{id}/purchases',
            uriVariables: [
                'id' => new Link(fromClass: User::class,
                    toProperty: 'buyer')
            ]
        ),
    ]
),
    ApiFilter(SearchFilter::class, properties: [
        'buyer' => 'exact',
        'course' => 'exact',
    ]),

]
class Purchase
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['purchase:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['purchase:read'])]
    private ?User $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['purchase:read'])]
    private ?Content $course = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getCourse(): ?Content
    {
        return $this->course;
    }

    public function setCourse(?Content $course): self
    {
        $this->course = $course;

        return $this;
    }
}