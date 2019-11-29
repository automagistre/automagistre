<?php

declare(strict_types=1);

namespace App\Utils;

use LogicException;
use function sprintf;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class FormUtil
{
    public static function getErrorMessages(FormInterface $form): array
    {
        return self::getErrors($form);
    }

    private static function getErrors(FormInterface $form, bool $root = true): array
    {
        $name = $form->getName();
        if (!$root) {
            $name = '['.$name.']';
        }

        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            if (!$error instanceof FormError) {
                throw new LogicException(sprintf('$error must be instance of "%s"', FormError::class));
            }

            if ($root) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[$name][] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            /** @var FormInterface $child */
            if (!$child->isValid()) {
                foreach (self::getErrors($child, false) as $childName => $childErrors) {
                    $errors[$name.$childName] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
