<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Themes;
use App\Entity\Courses;
use App\Entity\Lessons;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
  public function index(): Response
  {
    //return parent::index();
    /*$routeBuilder = $this->container->get(AdminUrlGenerator::class);
    $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();
    return $this->redirect($url);*/

    // Option 1. You can make your dashboard redirect to some common page of your backend
    //
    // 1.1) If you have enabled the "pretty URLs" feature:
    // return $this->redirectToRoute('admin_user_index');
    //
    // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
    // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
    // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

    // Option 2. You can make your dashboard redirect to different pages depending on the user
    //
    // if ('jane' === $this->getUser()->getUsername()) {
    //     return $this->redirectToRoute('...');
    // }

    // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
    // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
    //
    return $this->render('dashboard/dashboard.html.twig', [
      'user' => $this->getUser(),
    ]);
  }

  public function configureDashboard(): Dashboard
  {
    return Dashboard::new()
      ->setTitle('Knowledge Learning')
      ->setTranslationDomain('admin')
      ->setDefaultColorScheme('light')
    ;
  }

  public function configureMenuItems(): iterable
  {
    yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
    yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
    yield MenuItem::linkToCrud('Themes', 'fas fa-icons', Themes::class);
    yield MenuItem::linkToCrud('Courses', 'fas fa-book', Courses::class);
    yield MenuItem::linkToCrud('Lessons', 'fas fa-video', Lessons::class);
  }
}
