<?php

namespace App\Twig;

use App\Entity\Event;
use App\Entity\User;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CustomFiltersExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('display_show_button', [$this,'displayShowButton']),
            new TwigFilter('display_edit_publish_buttons', [$this,'displayEditPublishButtons']),
            new TwigFilter('display_cancel_button', [$this,'displayCancelButton']),
            new TwigFilter('display_unsub_button', [$this,'displayUnsubButton']),
            new TwigFilter('display_sub_button', [$this,'displaySubButton']),
        ];
    }

    public function displayShowButton(Event $event, User $user)
    {
        return !($event->getEventPlanner() === $user && $event->getStatus()->getName()=='created');
    }

    public function displayEditPublishButtons(Event $event, User $user)
    {
        return !$this->displayShowButton($event,$user);

    }

    public function displayCancelButton(Event $event, User $user)
    {
        return ($event->getEventPlanner() === $user && $event->getStatus()->getName()=='open'
                || $user->isAdmin() === true);
    }

    public function displayUnsubButton(Event $event, User $user)
    {
        return ($event->getEventPlanner() !== $user
            && $event->getStatus()->getName()=='open'
            && $event->getAttendants()->contains($user));
    }

    public function displaySubButton (Event $event, User $user)
    {
        $dateTime = new DateTime();
        return ($event->getEventPlanner() !== $user
            && $event->getStatus()->getName()=='open'
            && !$event->getAttendants()->contains($user))
            && $dateTime < $event->getRegistrationDeadline()
            && count($event->getAttendants()) != $event->getMaxRegistrations();
    }

}