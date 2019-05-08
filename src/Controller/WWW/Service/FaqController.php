<?php

declare(strict_types=1);

namespace App\Controller\WWW\Service;

use App\Utils\FormUtil;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class FaqController extends AbstractController
{
    /**
     * @Route("/faq", name="faq", methods={"POST"})
     */
    public function __invoke(Request $request, Swift_Mailer $mailer): Response
    {
        $form = $this->createFormBuilder(null, [
            'action' => $this->generateUrl('www_faq'),
        ])
            ->add('name')
            ->add('email', EmailType::class)
            ->add('question', TextareaType::class)
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = (object) $form->getData();

            $message = (new Swift_Message())
                ->setFrom(['no-reply@automagistre.ru' => 'Автомагистр'])
                ->setReplyTo($data->email)
                ->setTo(['info@automagistre.ru'])
                ->setSubject(\sprintf('FAQ Вопрос от %s', $data->name))
                ->setBody(<<<TEXT
                    Имя: {$data->name}
                    Почта: {$data->email}
                    Вопрос: {$data->question}
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
