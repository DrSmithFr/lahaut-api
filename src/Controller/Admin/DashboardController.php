<?php

namespace App\Controller\Admin;

use App\Enum\RoleEnum;
use App\Repository\Booking\BookingRepository;
use App\Repository\UserRepository;
use App\Service\Platform\PlatformFeeService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AdminPageController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PlatformFeeService $platformFeeService,
        private readonly BookingRepository $bookingRepository,
    ) {
    }

    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(): Response
    {
        $customers = $this
            ->userRepository
            ->countWithRole(RoleEnum::CUSTOMER);

        $monitors = $this
            ->userRepository
            ->countWithRole(RoleEnum::MONITOR);

        $bookingsTotal = $this
            ->bookingRepository
            ->totalAmountThisMonth();

        $platformFee = $this
            ->platformFeeService
            ->computeFee($bookingsTotal);

        return $this->render(
            'admin/dashboard.html.twig',
            [
                'customers' => $customers,
                'monitors' => $monitors,
                'bookings' => $bookingsTotal,
                'platformFee' => $platformFee,
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
