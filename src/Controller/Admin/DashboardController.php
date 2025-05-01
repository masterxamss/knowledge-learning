<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Themes;
use App\Entity\Courses;
use App\Entity\Lessons;
use App\Entity\Chapters;
use App\Entity\Badges;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Certifications;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardController
 *
 * This controller is responsible for configuring and displaying the administration
 * dashboard of the "Knowledge Learning" platform using EasyAdminBundle.
 *
 * The dashboard shows useful statistics such as monthly sales, client sales,
 * best-selling courses and lessons, total number of users and certifications.
 *
 * @package App\Controller\Admin
 */
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $em;

  /**
   * DashboardController constructor.
   *
   * @param EntityManagerInterface $em The Doctrine entity manager
   */
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  /**
   * Displays the main dashboard with relevant statistics.
   *
   * @return Response
   */
  public function index(): Response
  {
    try {
      $monthlySales = $this->em->getRepository(Order::class)->getTotalMonthlySells();
      $totalSalesPerClient = $this->em->getRepository(Order::class)->getTotalSellsPerClient();
      $totalSalesCurrentYear = $this->em->getRepository(Order::class)->getTotalSalesInCurrentYear();
      $courseMostSale = $this->em->getRepository(OrderItem::class)->getMostSaleCourse();
      $lessonMostSale = $this->em->getRepository(OrderItem::class)->getMostSaleLesson();
      $totalUsers = $this->em->getRepository(User::class)->count([]);
      $totalCertifications = $this->em->getRepository(Certifications::class)->count([]);

      return $this->render('dashboard/dashboard.html.twig', [
        'monthlySales' => $monthlySales,
        'clientSales' => $totalSalesPerClient,
        'courseMostSale' => $courseMostSale,
        'lessonMostSale' => $lessonMostSale,
        'totalSalesCurrentYear' => number_format($totalSalesCurrentYear, 2, '.', ''),
        'totalUsers' => $totalUsers,
        'totalCertifications' => $totalCertifications
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', $e->getMessage());
      return $this->redirectToRoute('app_home');
    }
  }

  /**
   * Configures the dashboard title and layout.
   *
   * @return Dashboard
   */
  public function configureDashboard(): Dashboard
  {
    return Dashboard::new()
      ->setTitle('Knowledge Learning')
      ->setTranslationDomain('admin')
      ->setDefaultColorScheme('light');
  }

  /**
   * Defines the sidebar menu items available in the dashboard.
   *
   * @return iterable
   */
  public function configureMenuItems(): iterable
  {
    yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
    yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
    yield MenuItem::linkToCrud('Themes', 'fas fa-icons', Themes::class);
    yield MenuItem::linkToCrud('Courses', 'fas fa-book', Courses::class);
    yield MenuItem::linkToCrud('Lessons', 'fas fa-video', Lessons::class);
    yield MenuItem::linkToCrud('Chapters', 'fas fa-book-open', Chapters::class);
    yield MenuItem::linkToCrud('Badges', 'fas fa-tag', Badges::class);
    yield MenuItem::linkToCrud('Orders', 'fas fa-clipboard', Order::class);
  }
}
