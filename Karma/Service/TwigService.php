<?php namespace Karma\Service;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigService
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(ROOT_DIR . '/views/twig');

        $this->twig = new Environment($loader, [
            'cache' => ROOT_DIR . '/views/twig_c',
        ]);
    }

    public function render($name, array $context = [])
    {
        return $this->twig->render($name, $context);
    }
}
