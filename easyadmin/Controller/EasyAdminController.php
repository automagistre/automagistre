<?php

declare(strict_types=1);

namespace EasyCorp\Bundle\EasyAdminBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\FilterRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Search\Autocomplete;
use EasyCorp\Bundle\EasyAdminBundle\Search\Paginator;
use EasyCorp\Bundle\EasyAdminBundle\Search\QueryBuilderFactory;
use EasyCorp\Bundle\EasyAdminBundle\Security\AuthorizationChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * The controller used to render all the default EasyAdmin actions.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class EasyAdminController extends AbstractController
{
    use AdminControllerTrait;

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            Autocomplete::class,
            ManagerRegistry::class,
            ConfigManager::class,
            Paginator::class,
            QueryBuilderFactory::class,
            PropertyAccessorInterface::class,
            FilterRegistry::class,
            AuthorizationChecker::class,
            EventDispatcherInterface::class,
        ]);
    }
}
