<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Subscribers;

use Alahaxe\HoneypotBundle\Services\CounterMeasureManager;
use Alahaxe\HoneypotBundle\Services\UrlDetectorService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class HoneypotHttpSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected CounterMeasureManager $counterMeasureManager,
        protected UrlDetectorService $urlDetectorService,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        $events[KernelEvents::EXCEPTION][] = ['onException'];
        $events[KernelEvents::REQUEST][] = ['onRequest'];

        return $events;
    }

    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // sub request for error pages
        if ($request->attributes->has('exception')) {
            return;
        }

        $honeypotResponse = $this->counterMeasureManager->onRequest($request);

        if ($honeypotResponse !== null) {
            $event->setResponse($honeypotResponse);
        }
    }

    public function onException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        if (!$e instanceof NotFoundHttpException) {
            return;
        }

        $honeypotTriggered = $this->urlDetectorService->getRequestHoneypot(
            $event->getRequest()
        );

        if ($honeypotTriggered === null) {
            return;
        }

        $this->counterMeasureManager->react(
            $event->getRequest(),
            $honeypotTriggered
        );
    }
}
