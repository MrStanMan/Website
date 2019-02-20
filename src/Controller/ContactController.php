<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    public function contact(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $contactFormData = $form->getData();

            $message = (new \Swift_Message('Er is contact opgenomen'))
                ->setFrom($contactFormData['email'])
                ->setTo('stan@fizz.nl')
                ->setBody(
                    'Aangevraagt door: ' . $contactFormData['email'] .
                    '<br />' .
                    'Bericht: ' . $contactFormData['message']
                    , 'text/html'
                )
                ->addPart(
                    'Aangevraagt door: ' . $contactFormData['email'] .
                    'Bericht: ' . $contactFormData['message']
                    , 'text/plain'
                );


            $mailer->send($message);

            $this->addFlash('success', 'Wij hebben uw verzoek ontvangen.');

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'contact_form' => $form->createView()
        ]);
    }
}
