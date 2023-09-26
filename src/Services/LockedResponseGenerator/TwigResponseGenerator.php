<?php

namespace Alahaxe\HoneypotBundle\Services\LockedResponseGenerator;

use Alahaxe\HoneypotBundle\Services\LockedResponseGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class TwigResponseGenerator implements LockedResponseGeneratorInterface
{
    public function __construct(
        protected Environment $twig,
        protected string $template,
        protected int $statusCode = Response::HTTP_LOCKED,
    ) {

    }
    public function generateResponse(): Response
    {
        try {
            return new Response(
                $this->twig->render($this->template),
                $this->statusCode
            );
        } catch (\Throwable $e) {
            dump($e);
            die;
        }

    }
}
