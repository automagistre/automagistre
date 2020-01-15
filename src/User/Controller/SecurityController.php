<?php

declare(strict_types=1);

namespace App\User\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"GET"})
     */
    public function login(FormFactoryInterface $formFactory, AuthenticationUtils $authUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect('/');
        }

        $form = $formFactory->createNamedBuilder('', FormType::class, null, [
            'action' => $this->generateUrl('admin_login_check'),
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
        ])
            ->add('_username')
            ->add('_password', PasswordType::class)
            ->getForm();

        return $this->render('admin/security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $authUtils->getLastAuthenticationError(),
        ]);
    }
}
