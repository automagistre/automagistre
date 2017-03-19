<?php

namespace AppBundle\Twig\Extension;

use Doctrine\Common\Util\ClassUtils;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessor;
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
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    public function __construct(
        ConfigManager $configManager,
        RouterInterface $router,
        RequestStack $requestStack,
        PropertyAccessor $propertyAccessor
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
        ];
    }

    public function getEasyAdminUrlForEntity($entity, array $parameters = []): string
    {
        $config = $this->configManager->getEntityConfigByClass(ClassUtils::getRealClass(get_class($entity)));
        $request = $this->requestStack->getMasterRequest();

        return $this->router->generate('easyadmin', array_merge([
            'id' => $this->propertyAccessor->getValue($entity, 'id'),
            'action' => 'edit',
            'entity' => $config['name'],
            'referer' => urlencode($request->getUri()),
        ], $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app';
    }
}
