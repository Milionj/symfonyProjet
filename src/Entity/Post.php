<?php

namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
// Mettre des contraintes sur vos props 
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('slug', message:'Ce slug existe déjà.')]
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
class Post
{
    const STATES = ['DRAFT', 'PUBLISHED'];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'post', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(length: 255, unique:true)]
    #[Assert\NotBlank(message:'Le titre ne peut pas être vide.') ]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank() ]
    private ?string $content = null;

    #[ORM\Column(length: 255, unique:true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $state = Post::STATES[0];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Categories::class, inversedBy:'post')]
    private Collection $category;

    public function __construct(){
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Décorateur permet de déclancher des fonctions à un certain moment du cycle de vie de l'entité
    #[ORM\PreUpdate]
    public function preUpdate(){
        $this->updatedAt = new \DateTimeImmutable();
    }


    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->slug = (new Slugify())->slugify($this->title);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    
    /**
     * @return Collection<int, Categories>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategories(Categories $categories): static
    {
        if (!$this->category->contains($categories)) {
            $this->category->add($categories);
        }

        return $this;
    }

    public function removeCategories(Categories $categories): static
    {
        $this->category->removeElement($categories);

        return $this;
    }
}