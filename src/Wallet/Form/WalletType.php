<?php

declare(strict_types=1);

namespace App\Wallet\Form;

use App\Doctrine\Registry;
use App\Identifier\IdentifierFormatter;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletId;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_map;

final class WalletType extends AbstractType
{
    private Registry $registry;

    private IdentifierFormatter $formatter;

    public function __construct(Registry $registry, IdentifierFormatter $formatter)
    {
        $this->registry = $registry;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'placeholder' => 'Выберите счёт',
            'choice_loader' => new CallbackChoiceLoader(function (): array {
                return array_map(
                    static fn (Wallet $wallet): WalletId => $wallet->toId(),
                    $this->registry->findBy(Wallet::class),
                );
            }),
            'choice_label' => fn (WalletId $walletId) => $this->formatter->format($walletId),
            'choice_value' => fn (?WalletId $walletId) => null === $walletId ? null : $walletId->toString(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
