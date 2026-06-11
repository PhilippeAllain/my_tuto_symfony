<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        /* TODO:  : Supprimer ça

        $data->name = 'John Doe';
        $data->email = 'john';
        $data->message = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas, eaque.';
        */

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            try {
            $mail = (new TemplatedEmail())
            ->to($data->service)
            ->from($data->email)
            ->subject('Demande de contact')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['data' => $data]);
            $mailer->send($mail);
            $this->addFlash('success', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('contact');
            } catch(\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyer votre mail');
            }
        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
