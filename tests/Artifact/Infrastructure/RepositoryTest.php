<?php declare(strict_types=1);

namespace Ramsterhad\TenaciousLibrary\Test\Artifact\Infrastructure;

use PHPUnit\Framework\TestCase;
use Ramsterhad\TenaciousLibrary\Artifact\Infrastructure\Exception\NoArtifact;
use Ramsterhad\TenaciousLibrary\Artifact\Infrastructure\Repository;
use Ramsterhad\TenaciousLibrary\Shared\HttpRequest\Curl;

final class RepositoryTest extends TestCase
{
    public function testGeneral(): void
    {
        $this->markTestSkipped('This test exists only until the class is fully testable.');

        (new Repository(
            new Curl(),
            '<replace with bearer token>'
        ))
            ->getLatestArtifactWithContent()
            ->getContent()
            ->extractTo(__DIR__ . '/');

        $this->assertFileExists(__DIR__ . '/data.json');
        $this->assertFileEquals(__DIR__ . '/expected.json', __DIR__ . '/data.json');
    }

    public function testGetAllArtifacts(): void
    {
        $mock = $this->getMockBuilder(Curl::class)
            ->onlyMethods(['get', 'getResponse'])
            ->getMock();

        $mock->method('get')->willReturn($mock);
        $mock->method('getResponse')->willReturn($this->artifactProviderValid());

        $r = new Repository($mock, 'test');
        $artifacts = $r->getAllArtifacts();

        $this->assertIsArray($artifacts);
        $this->assertCount(1, $artifacts);
        $this->assertEquals('1658639054', $artifacts[0]->getId());
        $this->assertEquals('2024-07-02T08:26:41Z', $artifacts[0]->getCreatedAt());
        $this->assertEquals('test_download_url', $artifacts[0]->getArchiveDownloadUrl());
    }

    public function testGetAllArtifactsNoArrayException(): void
    {
        $this->expectException(NoArtifact::class);
        $this->expectExceptionMessage('No array');

        $mock = $this->getMockBuilder(Curl::class)
            ->onlyMethods(['get', 'getResponse'])
            ->getMock();

        $mock->method('get')->willReturn($mock);
        $mock->method('getResponse')->willReturn('');

        $r = new Repository($mock, 'test');
        $r->getAllArtifacts();
    }

    public function testGetAllArtifactsNoArrayKeyArtifactException(): void
    {
        $this->expectException(NoArtifact::class);
        $this->expectExceptionMessage('No array key "artifacts"');

        $mock = $this->getMockBuilder(Curl::class)
            ->onlyMethods(['get', 'getResponse'])
            ->getMock();

        $mock->method('get')->willReturn($mock);
        $mock->method('getResponse')->willReturn($this->artifactProviderMissingArtifactsKey());

        $r = new Repository($mock, 'test');
        $r->getAllArtifacts();
    }

    public function testGetAllArtifactsNoArtifactException(): void
    {
        $this->expectException(NoArtifact::class);
        $this->expectExceptionMessage('No artifacts');

        $mock = $this->getMockBuilder(Curl::class)
            ->onlyMethods(['get', 'getResponse'])
            ->getMock();

        $mock->method('get')->willReturn($mock);
        $mock->method('getResponse')->willReturn($this->artifactProviderNoArtifacts());

        $r = new Repository($mock, 'test');
        $r->getAllArtifacts();
    }




    public function artifactProviderValid(): string
    {
        return '{
    "total_count": 1,
    "artifacts": [
        {
            "id": 1658639054,
            "node_id": "MDg6QXJ0aWZhY3QxNjU4NjM5MDU0",
            "name": "results",
            "size_in_bytes": 444,
            "url": "test_url",
            "archive_download_url": "test_download_url",
            "expired": false,
            "created_at": "2024-07-02T08:26:41Z",
            "updated_at": "2024-07-02T08:26:41Z",
            "expires_at": "2024-09-30T08:22:26Z",
            "workflow_run": {
                "id": 9757479569,
                "repository_id": 718311293,
                "head_repository_id": 718311293,
                "head_branch": "main",
                "head_sha": "20648c84eaf6422b6c357db6a90b96257968bc97"
            }
        }
    ]
}';
    }

    public function artifactProviderMissingArtifactsKey(): string
    {
        return '{
    "total_count": 1,
    "test": [
        {
        }
    ]
}';
    }

    public function artifactProviderNoArtifacts(): string
    {
        return '{
    "total_count": 1,
    "artifacts": []
}';
    }
}
