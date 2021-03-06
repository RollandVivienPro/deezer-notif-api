<?php

namespace App\Entity\Music;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Entity(repositoryClass="App\Repository\Music\PodcastRepository")
 */
class Podcast
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"listNotifs"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"listNotifs"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"listNotifs"})
     */
    private $coverImgUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCoverImgUrl(): ?string
    {
        return $this->coverImgUrl;
    }

    public function setCoverImgUrl(?string $coverImgUrl): self
    {
        $this->coverImgUrl = $coverImgUrl;

        return $this;
    }
}
