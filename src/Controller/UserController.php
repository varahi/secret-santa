<?php

namespace App\Controller;

use App\Entity\Gift;
use App\Entity\User;
use App\Form\GiftFormType;
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
    public function sendGift(
        Request $request,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        Mailer $mailer
    ): Response {
        $gift = new Gift();
        $form = $this->createForm(GiftFormType::class, $gift);
        $form->handleRequest($request);

        $users = $userRepository->findAll();
        $users2 = $users;

        if (count($users) > 1) {
            for ($i = 0; $i < count($users); $i++) {
                // Get unique santa:
                do {
                    $random = rand(0, count($users2) - 1);
                } while ($users[$i] == $users2[$random]);

                // Show message
                $messages[] = $users2[$random]->getFullName() .' '.$translator->trans('Will be santa for', [], 'messages') .' '. $users[$i]->getFullName();

                // Create mail array
                $mailArray[$i] = [
                    'senderEmail' => $users2[$random]->getEmail(),
                    'receiverEmail' => $users[$i]->getEmail()
                ];

                // Unset santa
                unset($users2[$random]);

                // Normalize array
                $users2 = array_values($users2);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                if (is_array($mailArray) && isset($mailArray)) {
                    foreach ($mailArray as $mail) {
                        $subject = $translator->trans('New gift frim secret Santa', [], 'messages');
                        $mailer->sendSantaEmail($mail['senderEmail'], $mail['receiverEmail'], $subject, 'emails/santa_gift.html.twig', $gift);
                    }
                }

                $message = $translator->trans('User created', [], 'messages');
                $notifier->send(new Notification($message, ['browser']));
                return $this->redirectToRoute("app_create_user");
            }
        }

        return $this->render('user/send_gift.html.twig', [
            'messages' => $messages,
            'form' => $form
        ]);
    }

    #[Route('/create-user', name: 'app_create_user')]
    public function createUser(
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
            //$entityManager->persist($user);
            //$entityManager->flush();

            //$subject = $translator->trans('User created', [], 'messages');
            $mailer->sendEmail('Test email', 'emails/test.html.twig');

            $message = $translator->trans('User created', [], 'messages');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_create_user");
        }

        return $this->render('user/create_user.html.twig', [
            'form' => $form
        ]);
    }
}
