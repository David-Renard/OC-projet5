<?php

declare(strict_types=1);

namespace App\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Service\Http\Session\Session;
final class View
{
    private Environment $twig;
    public function __construct(private Session $session)
    {
        $loader = new FilesystemLoader('../templates');
        $this->twig = new Environment($loader);
    }
    public function render(array $data): string
    {
        $data['data']['session'] = $this->session->toArray();
        $data['data']['flashes'] = $this->session->getFlashes();
        return $this->twig->render("FrontOffice/{$data['template']}.html.twig", $data['data']);
    }
}
