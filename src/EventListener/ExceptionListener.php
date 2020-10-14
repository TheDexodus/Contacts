<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ExceptionListener
 */
class ExceptionListener
{
    private Environment $twig;

    /**
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param ExceptionEvent $event
     *
     * @throws LoaderError|RuntimeError|SyntaxError
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new Response();

        if ($exception instanceof HttpExceptionInterface) {
            $response->setContent($this->getContent($exception->getStatusCode(), $exception));
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setContent($this->getContent($exception->getCode(), $exception));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }

    /**
     * @param int        $code
     *
     * @param Throwable $exception
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function getContent(int $code, Throwable $exception): string
    {
        switch ($code) {
            case 404:
                return $this->twig->render('exception/error404.html.twig');
            case 403:
                return $this->twig->render('exception/error403.html.twig');
            case 500:
                return $this->twig->render('exception/error500.html.twig');
        }

        return $this->twig->render('exception/custom_error.html.twig', ['code' => $code, 'exception' => $exception]);
    }
}
