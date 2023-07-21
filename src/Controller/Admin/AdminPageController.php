<?php

namespace App\Controller\Admin;

use App\Entity\Activity\ActivityLocation;
use App\Entity\Activity\ActivityType;
use App\Entity\Activity\Place\LandingPoint;
use App\Entity\Activity\Place\MeetingPoint;
use App\Entity\Activity\Place\TakeOffPoint;
use App\Entity\Booking\Booking;
use App\Entity\Chat\Conversation;
use App\Entity\Chat\Message;
use App\Entity\Slot\Slot;
use App\Entity\Slot\SlotLock;
use App\Entity\Slot\SlotProposed;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

abstract class AdminPageController extends AbstractDashboardController
{
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);

        yield MenuItem::section('Activities');
        yield MenuItem::subMenu('Address', 'fa fa-signs-post')->setSubItems([
            MenuItem::linkToCrud('Meeting', 'fa fa-handshake', MeetingPoint::class),
            MenuItem::linkToCrud('TakeOff', 'fa fa-play', TakeOffPoint::class),
            MenuItem::linkToCrud('Landing', 'fa fa-flag-checkered', LandingPoint::class),
        ]);

        yield MenuItem::linkToCrud('Location', 'fa fa-map-location', ActivityLocation::class);
        yield MenuItem::linkToCrud('Type', 'fa fa-star', ActivityType::class);
        yield MenuItem::linkToCrud('Slots proposed', 'fa fa-comments', SlotProposed::class);
        yield MenuItem::section('Chat');
        yield MenuItem::linkToCrud('Conversation', 'fa fa-comments', Conversation::class);
        yield MenuItem::linkToCrud('Message', 'fa fa-comment', Message::class);

        yield MenuItem::section('Booking');
        yield MenuItem::subMenu('Slots', 'fa fa-signs-post')->setSubItems([
            MenuItem::linkToCrud('Available', 'fa fa-comment', Slot::class),
            MenuItem::linkToCrud('Reserved', 'fa fa-comment', SlotLock::class),
        ]);
        yield MenuItem::linkToCrud('Bookings', 'fa fa-comment', Booking::class);
    }
}
