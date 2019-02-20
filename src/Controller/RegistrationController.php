<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse('/StanWebsite/public/');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setToken($this->generateRandomToken());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            sleep(1);

            $message = (new \Swift_Message('Activeer uw mail!'))
                ->setFrom('noreply@stanwebsite.nl')
                ->setTo($user->getEmail())
                ->setBody(
                    'Hallo ' . $user->getName() . '! <br /> Klik hier om uw account te activeren: <a href="' . $this->getParameter('router.request_context.base_url') . '/activate/' . $user->getToken() . '/' . $user->getId() . '">Activeer</a>'
                    , 'text/html'
                );


            $mailer->send($message);
            $this->addFlash('success', 'Er is een activatiemail gestuurd naar het ingevulde mail adress!');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    function generateRandomToken($length = 30)
    {
        return substr(
            str_shuffle(
                str_repeat(
                    $x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length/strlen($x)
                    )
                )
            ),1,$length
        );
    }

}
