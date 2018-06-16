<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Method("GET")
     */
    public function loginAction(FormFactoryInterface $formFactory, AuthenticationUtils $authUtils): Response
    {
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
