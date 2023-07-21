<?php

namespace App\Controller\Admin;

use App\Enum\RoleEnum;
use App\Repository\Booking\BookingRepository;
use App\Repository\UserRepository;
use App\Service\Platform\PlatformFeeService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AdminPageController
{
    public final const CHART_DAYS = 15;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PlatformFeeService $platformFeeService,
        private readonly BookingRepository $bookingRepository,
        private readonly ChartBuilderInterface $chartBuilder
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
                'customerChart' => $this->getCustomerChart(),
                'monitorChart' => $this->getMonitorChart(),
                'bookingChart' => $this->getBookingChart(),
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

    private function getCustomerChart(): Chart
    {
        $chartData = $this
            ->userRepository
            ->totalPerDay(RoleEnum::CUSTOMER, self::CHART_DAYS);

        return $this
            ->chartBuilder
            ->createChart(Chart::TYPE_BAR)
            ->setData([
                'labels' => array_keys($chartData),
                'datasets' => [
                    [
                        'label' => 'Customers per day',
                        'backgroundColor' => '#636767',
                        'borderColor' => '#495057',
                        'data' => array_values($chartData),
                    ],
                ],
            ]);
    }

    private function getMonitorChart(): Chart
    {
        $chartData = $this
            ->userRepository
            ->totalPerDay(RoleEnum::MONITOR, self::CHART_DAYS);

        return $this
            ->chartBuilder
            ->createChart(Chart::TYPE_BAR)
            ->setData([
                'labels' => array_keys($chartData),
                'datasets' => [
                    [
                        'label' => 'Monitors per day',
                        'backgroundColor' => '#886ab5',
                        'borderColor' => '#65498f',
                        'data' => array_values($chartData),
                    ],
                ],
            ]);
    }

    private function getBookingChart(): Chart
    {
        $chartData = $this
            ->bookingRepository
            ->totalPerDay(self::CHART_DAYS);

        return $this
            ->chartBuilder
            ->createChart(Chart::TYPE_LINE)
            ->setData([
                'labels' => array_keys($chartData),
                'datasets' => [
                    [
                        'label' => 'Bookings(€) per day',
                        'backgroundColor' => 'rgba(207,82,82,1)',
                        'borderColor' => 'rgba(121,9,9,1)',
                        'data' => array_values($chartData),
                    ],
                ],
            ]);
    }
}
