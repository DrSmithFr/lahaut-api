<?php

namespace App\Controller\Admin;

use App\Entity\Activity\ActivityLocation;
use App\Entity\Activity\ActivityType;
use App\Entity\Activity\Place\LandingPoint;
use App\Entity\Activity\Place\MeetingPoint;
use App\Entity\Activity\Place\TakeOffPoint;
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
        yield MenuItem::linkToCrud('Type', 'fa fa-star', ActivityType::class);
        yield MenuItem::linkToCrud('Location', 'fa fa-map-location', ActivityLocation::class);

        yield MenuItem::section('Places');
        yield MenuItem::linkToCrud('Meeting', 'fa fa-location-dot', MeetingPoint::class);
        yield MenuItem::linkToCrud('TakeOff', 'fa fa-plane-departure', TakeOffPoint::class);
        yield MenuItem::linkToCrud('Landing', 'fa fa-plane-arrival', LandingPoint::class);
    }
}
