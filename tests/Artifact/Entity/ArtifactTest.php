<?php declare(strict_types=1);

namespace Ramsterhad\TenaciousLibrary\Test\Artifact\Entity;

use PHPUnit\Framework\TestCase;
use Ramsterhad\TenaciousLibrary\Artifact\Entity\Artifact;
use ZipArchive;

final class ArtifactTest extends TestCase
{
    public function testGetters(): void
    {
        $a = new Artifact(665, 'foo', 'bar');
        $a->setContent(new ZipArchive());

        $this->assertEquals(665, $a->getId());
        $this->assertEquals('foo', $a->getCreatedAt());
        $this->assertEquals('bar', $a->getArchiveDownloadUrl());
        $this->assertInstanceOf(ZipArchive::class, $a->getContent());
    }
}
