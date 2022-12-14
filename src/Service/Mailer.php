<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use App\Entity\User;

class Mailer
{
    private $mailer;

    public function __construct(
        MailerInterface $mailer,
        Environment     $twig,
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(string $subject, string $template)
    {
        $date = new \DateTime();
        $email = (new TemplatedEmail())
            ->subject('Test')
            ->htmlTemplate($template)
            ->from('noreply@t3dev.ru')
            ->to('info@t3dev.ru')
            ->context([
                'date' => $date
            ]);

        $this->mailer->send($email);
    }


    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendSantaEmail(string $sender, string $receiver, string $subject, string $template, $gift)
    {
        $date = new \DateTime();
        $email = (new TemplatedEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->from($sender)
            ->to($receiver)
            ->context([
                'gift' => $gift,
                'date' => $date,
                'sender' => $sender,
                'receiver' => $receiver
            ]);

        $this->mailer->send($email);
    }
}
