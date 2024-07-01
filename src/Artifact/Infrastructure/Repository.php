<?php

declare(strict_types=1);

namespace Ramsterhad\TenaciousLibrary\Artifact\Infrastructure;

use Curl\Curl;
use Ramsterhad\TenaciousLibrary\Artifact\Entity\Artifact;
use Ramsterhad\TenaciousLibrary\Artifact\Infrastructure\Exception\CurlException;
use Ramsterhad\TenaciousLibrary\Artifact\Infrastructure\Exception\LocationHeaderNotFoundException;
use Ramsterhad\TenaciousLibrary\Artifact\Infrastructure\Exception\NoArtifact;
use ZipArchive;

use function file_get_contents;
use function file_put_contents;
use function preg_match;
use function sys_get_temp_dir;
use function trim;

class Repository
{
    public function __construct(private readonly string $bearerToken)
    {}
    /**
     * @return Artifact[]
     * @throws NoArtifact
     * @throws CurlException
     */
    public function getAllArtifacts(): array
    {
        $curl = (new Curl())
            ->setHeader('Accept', 'application/vnd.github+json')
            ->setHeader('Authorization', $this->bearerToken);
        $curl->get('https://api.github.com/repos/ramsterhad/tenacious-crawler/actions/artifacts');

        if ($curl->error) {
            throw new CurlException((string) $curl->error_code);
        }

        $artifactsAsJson = $curl->getResponse();
        $curl->close();

        $artifacts = json_decode($artifactsAsJson, true);

        if (!is_array($artifacts)) {
            throw new NoArtifact('No array');
        }

        if (!array_key_exists('artifacts', $artifacts)) {
            throw new NoArtifact('No array key "artifacts"');
        }

        if (count($artifacts['artifacts']) === 0) {
            throw new NoArtifact('No artifacts');
        }

        $artifactList = [];
        foreach ($artifacts['artifacts'] as $item) {
            $artifactList[] = new Artifact(
                $item['id'],
                $item['created_at'],
                $item['archive_download_url'],
            );
        }

        return $artifactList;
    }

    /**
     * @throws CurlException
     * @throws NoArtifact
     */
    public function getLatestArtifact(): Artifact
    {
        return $this->getAllArtifacts()[0];
    }

    /**
     * @throws CurlException
     * @throws LocationHeaderNotFoundException
     * @throws NoArtifact
     */
    public function getLatestArtifactWithContent(): Artifact
    {
        $artifact = $this->getLatestArtifact();
        $this->downloadContentOfArtifact($artifact);
        return $artifact;
    }

    /**
     * @throws LocationHeaderNotFoundException
     */
    public function downloadContentOfArtifact(Artifact $artifact): void
    {
        $curl = (new Curl())
            ->setHeader('Accept', 'application/vnd.github+json')
            ->setHeader('Authorization', $this->bearerToken);
        $curl->setOpt(CURLOPT_HEADER, true);

        $headers = $curl->get($artifact->getArchiveDownloadUrl());
        $curl->close();

        preg_match('/location: (.*)/i', $headers->getResponse(), $matches);

        if (count($matches) !== 2) {
            throw new LocationHeaderNotFoundException('Invalid preg match result.');
        }

        $location = trim($matches[1]);
        $destination = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'result.zip';

        file_put_contents(
            $destination,
            file_get_contents($location)
        );

        $zip = new ZipArchive();
        $zip->open($destination);
        $artifact->setContent($zip);
    }
}
