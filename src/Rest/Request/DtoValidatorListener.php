<?php

declare(strict_types=1);

namespace App\Rest\Request;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DtoValidatorListener implements EventSubscriberInterface
{
    private DtoDetector $detector;

    private ValidatorInterface $validator;

    private DtoValidationViolationController $controller;

    public function __construct(
        DtoDetector $detector,
        ValidatorInterface $validator,
        DtoValidationViolationController $controller
    ) {
        $this->detector = $detector;
        $this->validator = $validator;
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerArgumentsEvent::class => 'onControllerArguments',
        ];
    }

    public function onControllerArguments(ControllerArgumentsEvent $event): void
    {
        foreach ($event->getArguments() as $argument) {
            if (!$this->detector->isDto($argument)) {
                continue;
            }

            $violationList = $this->validator->validate($argument);

            if (0 < count($violationList)) {
                $event->setArguments([$violationList]);
                $event->setController($this->controller);
            }

            return;
        }
    }
}
