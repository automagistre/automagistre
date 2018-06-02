<?php

declare(strict_types=1);

namespace App\Controller\WWW;

use App\Utils\FormUtil;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/service/{brand}", requirements={"brand": "nissan|toyota|infinity|lexus"})
 */
final class ServiceController extends Controller
{
    /**
     * @Route("/", name="service")
     */
    public function service(): Response
    {
        return $this->render('www/service.html.twig', [
        ]);
    }

    /**
     * @Route("/repair", name="repair")
     */
    public function repair(): Response
    {
        return $this->render('www/repair.html.twig', [
        ]);
    }

    /**
     * @Route("/diagnostics/{type}", name="diagnostics", requirements={"type": "free|comp"})
     */
    public function diagnostics(Request $request, Swift_Mailer $mailer, string $type): Response
    {
        $data = new class() {
            public $name;
            public $telephone;
            public $date;
            public $checkbox = true;
        };

        $form = $this->createFormBuilder($data)
            ->add('name')
            ->add('telephone')
            ->add('date')
            ->add('checkbox', CheckboxType::class)
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = (new \Swift_Message())
                ->setFrom(['no-reply@automagistre.ru' => 'Автомагистр'])
                ->setTo(['info@automagistre.ru'])
                ->setSubject(sprintf('Запись на %s диагностику', [
                    'free' => 'бесплатную',
                    'comp' => 'компьютерную',
                ][$type]))
                ->setBody(<<<TEXT
Имя: $data->name
Телефон: $data->telephone
Дата: $data->date
TEXT
                );

            $mailer->send($message);

            return new Response('OK');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->json([
                'error' => FormUtil::getErrorMessages($form),
            ]);
        }

        if ('comp' === $type) {
            return $this->render('www/diagnostics_comp.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        if ('free' === $type) {
            return $this->render('www/diagnostics_free.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        throw new LogicException('Unreachable statement');
    }

    /**
     * @Route("/tire", name="type")
     */
    public function tire(): Response
    {
        return $this->render('www/tire_service.html.twig');
    }

    /**
     * @Route("/brands", name="brands")
     */
    public function brands(Request $request, Swift_Mailer $mailer): Response
    {
        $data = new class() {
            public $name;
            public $telephone;
            public $date;
            public $checkbox = true;
        };

        $form = $this->createFormBuilder($data)
            ->add('name')
            ->add('telephone')
            ->add('date')
            ->add('checkbox', CheckboxType::class)
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = (new \Swift_Message())
                ->setFrom(['no-reply@automagistre.ru' => 'Автомагистр'])
                ->setTo(['info@automagistre.ru'])
                ->setSubject('Запись на бесплатную диагностику')
                ->setBody(<<<TEXT
Имя: $data->name
Телефон: $data->telephone
Дата: $data->date
TEXT
                );

            $mailer->send($message);

            return new Response('OK');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->json([
                'error' => FormUtil::getErrorMessages($form),
            ]);
        }

        return $this->render('www/brands.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/corporates", name="corporates")
     */
    public function corporates(Request $request, Swift_Mailer $mailer): Response
    {
        $data = new class() {
            public $name;
            public $telephone;
            public $checkbox = true;
        };

        $form = $this->createFormBuilder($data)
            ->add('name')
            ->add('telephone')
            ->add('checkbox', CheckboxType::class)
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = (new \Swift_Message())
                ->setFrom(['no-reply@automagistre.ru' => 'Автомагистр'])
                ->setTo(['info@automagistre.ru'])
                ->setSubject('Запись на корпоративное обслуживание')
                ->setBody(<<<TEXT
Имя: $data->name
Телефон: $data->telephone
TEXT
                );

            $mailer->send($message);

            return new Response('OK');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->json([
                'error' => FormUtil::getErrorMessages($form),
            ]);
        }

        return $this->render('www/corporates.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/price-list", name="price-list")
     */
    public function priceList(): Response
    {
        return $this->render('www/price_list.html.twig');
    }

    /**
     * @Route("/maintenance", name="maintenance")
     */
    public function maintenance(): Response
    {
        return $this->render('www/maintenance.html.twig');
    }

    /**
     * @Route("/contacts", name="contacts")
     */
    public function contacts(): Response
    {
        return $this->render('www/contacts.html.twig');
    }

    /**
     * @Route("/reviews", name="reviews")
     */
    public function reviews(): Response
    {
        return $this->render('www/reviews.html.twig');
    }

    /**
     * @Route("/privacy-policy", name="privacy-policy")
     */
    public function privacyPolicy(): Response
    {
        return $this->render('www/privacy_policy.html.twig');
    }

    /**
     * @Route("/faq", name="faq")
     * @Method("POST")
     */
    public function faq(Request $request, Swift_Mailer $mailer): Response
    {
        $data = new class() {
            public $name;
            public $email;
            public $question;
        };

        $form = $this->createFormBuilder($data, [
            'action' => $this->generateUrl('www_faq'),
        ])
            ->add('name')
            ->add('email', EmailType::class)
            ->add('question', TextareaType::class)
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = (new \Swift_Message())
                ->setFrom(['no-reply@automagistre.ru' => 'Автомагистр'])
                ->setReplyTo($data->email)
                ->setTo(['info@automagistre.ru'])
                ->setSubject(sprintf('FAQ Вопрос от %s', $data->name))
                ->setBody(<<<TEXT
Имя: $data->name
Почта: $data->email
Вопрос: $data->question
TEXT
                );

            $mailer->send($message);

            return new Response('OK');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->json([
                'error' => FormUtil::getErrorMessages($form),
            ]);
        }

        return $this->render('www/faq.html.twig', [
            'form' => $form->createView(),
            'scroll' => $request->query->getBoolean('scroll'),
        ]);
    }
}
