# Changelog

## 1.0.1

1.  Replace
      ```php
      use Ramsterhad\TenaciousLibrary\Artifact\Infrastructure\Repository;
      ```
    with
    ```php
    use Ramsterhad\TenaciousLibrary\Artifact\Infrastructure\Repository;
    use Ramsterhad\TenaciousLibrary\Shared\HttpRequest\Curl;
    ```
2.  Replace
    ```php
    (new Repository('<token>'))
    ->getLatestArtifactWithContent()
    ->getContent()
    ->extractTo(PUBLIC_DIRECTORY);
    ```
    with
    ```php
    (new Repository(
        new Curl(),
        '<token>'
    ))
    ->getLatestArtifactWithContent()
    ->getContent()
    ->extractTo(PUBLIC_DIRECTORY);
    ```
    
## 1.0.0
1.  Create file
    ```php
    <?php declare(strict_types=1);
    
    require_once '../vendor/autoload.php';
    
    use Ramsterhad\TenaciousLibrary\Artifact\Infrastructure\Repository;
    
    const CURRENT_DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR;
    const PUBLIC_DIRECTORY = CURRENT_DIRECTORY . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
    
    (new Repository('<token>'))
        ->getLatestArtifactWithContent()
        ->getContent()
        ->extractTo(PUBLIC_DIRECTORY);
    
    ```