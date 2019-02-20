<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountEditFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends AbstractController
{
    /**
     * @return Response
     */
    public function account()
    {
        return $this->render('pages/account/index.html.twig');
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(AccountEditFormType::class, $user);
        $form->handleRequest($request);


        if($this->getUser() !== $user)
        {
        $this->addFlash('warning', 'U kunt niet een account van iemand anders aanpassen');
            return $this->redirectToRoute('user_account');
        }
        elseif(in_array('ROLE_SUPER_ADMIN', $user->getRoles()))
        {
            $user->setRoles(array('ROLE_SUPER_ADMIN'));

            $this->addFlash('warning', 'Een Super Admin kan niet aangepast worden');

            return $this->redirectToRoute('user_account');
        }

        if ($form->isSubmitted() && $form->isValid())
        {

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Uw account is met success aangepast.');

            return $this->redirectToRoute('user_account');
        }

        return $this->render('pages/account/edit.html.twig', [
            'addForm' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @param $token
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function activate($token, User $user)
    {
        $em = $this->getDoctrine()->getManager();

        if($user->getActive() == 1)
        {
            $this->addFlash('warning', 'Dit account is al geactiveerd!');
        }
        elseif($user->getToken() == $token)
        {
            $user->setActive(1);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Uw account is met success geactiveerd!');

            return $this->redirectToRoute('app_login');
        }
        else
        {
            $this->addFlash('danger', 'Er is een verkeerde activatie link ingevuld.');
        }

        return $this->redirectToRoute('index');
    }


    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser() !== $user)
        {
            $this->addFlash('danger', 'You can\'t delete someone else his account.');
            return $this->redirectToRoute('user_account');
        }

        if(in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles()))
        {
            $this->addFlash('warning', 'U kunt geen admin verwijderen.');
            return $this->redirectToRoute('user_account');
        }

        $this->get('security.token_storage')->reset();

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Uw account is verwijderd!');

        return $this->redirectToRoute('index');
    }
}
