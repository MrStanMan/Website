<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddFormType;
use App\Form\EditFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends AbstractController
{
    /**
     * @return Response
     */
    public function showList()
    {
        $users = $this->getDoctrine()->getRepository('App:User')->findAll(array(), array('id' => 'ASC'));

        return $this->render('pages/admin/index.html.twig', [
                'users' => $users
            ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function addAction(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(AddFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Er is met success een gebruiker toegevoegd');

            return $this->redirectToRoute('admin_list');
        }

        return $this->render('pages/admin/add.html.twig', [
            'addForm' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editAction($id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->find($id);


        $form = $this->createForm(EditFormType::class, $user);
        $form->handleRequest($request);

        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles()))
        {
            $user->setRoles(array('ROLE_SUPER_ADMIN'));

            $this->addFlash('warning', 'Een Super Admin kan niet aangepast worden');

            return $this->redirectToRoute('admin_list');
        }
        elseif($id == $user->getID())
        {
            $this->addFlash('warning', 'U kunt niet het account aanpassen waar u op bent ingelogd. Log eerst in op een ander account en probeer het dan opnieuw');
            return $this->redirectToRoute('admin_list');
        }

        if ($form->isSubmitted() && $form->isValid())
        {

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Gebruiker met success aangepast.');

            return $this->redirectToRoute('admin_list');
        }

        return $this->render('pages/admin/edit.html.twig', [
            'addForm' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('App:User')->find($id);

        if (!$post)
        {
            $this->addFlash('danger', 'Gebruiker met id: ' . $id . ' bestaat niet.');
            return $this->redirectToRoute('admin_list');
        }
        elseif(in_array('ROLE_ADMIN', $post->getRoles()) || in_array('ROLE_SUPER_ADMIN', $post->getRoles()))
        {
            $this->addFlash('warning', 'U kunt geen admin verwijderen.');
            return $this->redirectToRoute('admin_list');
        }

        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Gebruiker met id: ' . $id . ' is successful verwijderd.');

        return $this->redirectToRoute('admin_list');
    }
}
