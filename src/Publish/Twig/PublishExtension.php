<?php

declare(strict_types=1);

namespace App\Publish\Twig;

use App\Publish\Entity\PublishView;
use App\Publish\Form\PublishDto;
use App\Publish\Form\PublishType;
use App\Shared\Doctrine\Registry;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PublishExtension extends AbstractExtension
{
    private Registry $registry;

    private FormFactoryInterface $formFactory;

    private EasyAdminRouter $router;

    public function __construct(Registry $registry, FormFactoryInterface $formFactory, EasyAdminRouter $router)
    {
        $this->registry = $registry;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('publish_form', function (string $id): FormView {
                $publishView = $this->registry->findOneBy(PublishView::class, ['id' => $id]);

                $publishDto = new PublishDto();
                $publishDto->id = $id;
                $publishDto->publish = null === $publishView ? true : !$publishView->published;

                return $this->formFactory->create(PublishType::class, $publishDto, [
                    'action' => $this->router->generate('Publish', 'index', [
                        'id' => $publishDto->id,
                        'referer' => true,
                    ]),
                ])->createView();
            }),
            new TwigFunction('is_published', function (string $id): bool {
                $result = $this->registry->connection()
                    ->fetchOne('SELECT published FROM publish_view WHERE id = :id', [
                        'id' => $id,
                    ])
                ;

                return (bool) $result;
            }),
        ];
    }
}
