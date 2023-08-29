<?php
declare(strict_types=1);

namespace App\Service\Http;
final class Response
{
    public function __construct(
        private string $content = '',
        private int $statusCode=200,
        private array $headers = []
    ){
    }

    public function send(): void
    {
        echo $this->statusCode . ' ' . implode(',', $this->headers);
        echo $this->content;
    }

    public function redirect(string $location = ''): string
    {
        header("Location: index.php" . $location);
        exit();
    }
}