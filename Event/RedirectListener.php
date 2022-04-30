<?php

namespace Autologic\Bundle\RedirectBundle\Event;

use Autologic\Bundle\RedirectBundle\Exception\RedirectionRuleNotFoundException;
use Autologic\Bundle\RedirectBundle\Service\RedirectService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectListener
{
    /**
     * @var RedirectService
     */
    private $redirectService;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * @param RedirectService      $redirectService
     * @param LoggerInterface|null $logger
     */
    public function __construct(RedirectService $redirectService, LoggerInterface $logger = null)
    {
        $this->redirectService = $redirectService;
        $this->logger = $logger;
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return bool
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if ($event->getThrowable() instanceof NotFoundHttpException) {
            try {
                $event->setResponse($this->redirectService->redirect($event->getRequest()));

                return true;
            } catch (RedirectionRuleNotFoundException $e) {
                if ($this->logger !== null) {
                    $this->logger->notice($e->getMessage());
                }
            }
        }

        return false;
    }
}
