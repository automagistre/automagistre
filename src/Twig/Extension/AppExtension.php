<?php

namespace App\Twig\Extension;

use Doctrine\Common\Util\ClassUtils;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig_SimpleFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(
        ConfigManager $configManager,
        RouterInterface $router,
        RequestStack $requestStack,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->configManager = $configManager;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('easyadmin_entity_path', [$this, 'getEasyAdminUrlForEntity']),
            new Twig_SimpleFunction('easyadmin_path', [$this, 'getEasyAdminUrlForEntity']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('easyadmin_path', [$this, 'getEasyAdminUrlForEntity']),
        ];
    }

    public function getEasyAdminUrlForEntity($entity, array $parameters = []): string
    {
        if (is_object($entity)) {
            $config = $this->configManager->getEntityConfigByClass(ClassUtils::getRealClass(get_class($entity)));

            $params = [
                'id'     => $this->propertyAccessor->getValue($entity, 'id'),
                'action' => 'edit',
            ];
        } else {
            $config = $this->configManager->getEntityConfig($entity);

            $params = [
                'action' => 'new',
            ];
        }

        $request = $this->requestStack->getMasterRequest();

        $params['entity'] = $config['name'];
        $params['referer'] = urlencode($request->getUri());

        return $this->router->generate('easyadmin', array_merge($params, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app';
    }
}
