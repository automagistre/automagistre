<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SecurityController extends Controller
{
    /**
     * @Route("/login", name="admin_login")
     */
    public function loginAction(FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->createNamedBuilder('', FormType::class, null, [
            'action' => $this->generateUrl('login_check'),
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
        ])
            ->add('_username')
            ->add('_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        return $this->render('login.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
