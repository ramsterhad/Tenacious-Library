<?php

declare(strict_types=1);

namespace Ramsterhad\TenaciousLibrary\Artifact\Entity;

use ZipArchive;

readonly class Artifact
{
    private ?ZipArchive $content;

    public function __construct(
        public int $id,
        public string $createdAt,
        public string $archiveDownloadUrl,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getArchiveDownloadUrl(): string
    {
        return $this->archiveDownloadUrl;
    }

    public function setContent(ZipArchive $content): void
    {
        $this->content = $content;
    }

    public function getContent(): ZipArchive
    {
        return $this->content;
    }
}
