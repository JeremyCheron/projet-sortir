<?php

namespace App\Services;

use App\Entity\Event;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{

    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function cancelEmail(Event $event): void
    {

        $mails = [];
        if ($event->getAttendants()->count() > 0) {
            $users = $event->getAttendants();
            foreach ($users as $user) {
                $mails[] = $user->getEmail();
            }
            $mailString = implode(',', $mails);

            $message = (new TemplatedEmail())
                ->from('admin@sortir.com')
                ->to($mailString)
                ->subject('Event canceled')
                ->htmlTemplate('mail/cancelEvent.html.twig');

            $this->mailer->send($message);
        }

    }

}