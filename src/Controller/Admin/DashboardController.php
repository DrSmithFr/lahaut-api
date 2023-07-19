<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AdminPageController
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(): Response
    {
        $users = $this
            ->userRepository
            ->findAll();

        $users = new ArrayCollection($users);

        $customers = $users->reduce(
            function (int $carry, User $user) {
                if ($user->hasRole(RoleEnum::CUSTOMER)) {
                    $carry++;
                }

                return $carry;
            },
            0
        );

        $monitors = $users->reduce(
            function (int $carry, User $user) {
                if ($user->hasRole(RoleEnum::MONITOR)) {
                    $carry++;
                }

                return $carry;
            },
            0
        );

        $bookings = 5000.0;

        // Fixed part
        $platform = 58.76;

        // Variable part (11.75%) for bookings > 10K€
        if ($bookings > 10000.0) {
            $platform += $bookings * 0.1175;
        }

        return $this->render(
            'admin/dashboard.html.twig',
            [
                'customers' => $customers,
                'monitors' => $monitors,
                'bookings' => $bookings,
                'platform' => $platform,
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // the name visible to end users
            ->setTitle('Antennae')

            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // by default, users can select between a "light" and "dark" mode for the
            // backend interface. Call this method if you prefer to disable the "dark"
            // mode for any reason (e.g. if your interface customizations are not ready for it)
            ->disableDarkMode(false)
            ->setLocales([
                'en' => '🇬🇧 English',
                'fr' => '🇫🇷 Français',
                'es' => '🇪🇸 Español',
            ]);
    }
}
