<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Mailer;

class UserController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function list(
        UserRepository $userRepository
    ): Response {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/send-gift', name: 'app_send_gift')]
    public function sendGift(): Response
    {
        return $this->render('user/sendGift.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/create-user', name: 'app_create_user')]
    public function home(
        Request $request,
        ManagerRegistry $doctrine,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        Mailer $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword('111');
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //$subject = $translator->trans('New admin registered', array(), 'messages');
            //$mailer->sendNewCompanyEmail($user, $subject, 'emails/new_user_creation.html.twig');

            $message = $translator->trans('User created', array(), 'messages');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_create_user");
        }

        return $this->render('user/createUser.html.twig', [
            'form' => $form
        ]);
    }
}
