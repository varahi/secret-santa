<?php

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Order;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Request as UserRequest;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;

class Mailer
{
    private $mailer;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendNewCompanyEmail(User $user, string $subject, string $template, $plainPassword)
    {
        $date = new \DateTime();
        $email = (new TemplatedEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->from('')
            ->to('')
            ->context([
                'user' => $user,
                'date' => $date,
                'plainPassword' => $plainPassword
            ]);

        $this->mailer->send($email);
    }
}
