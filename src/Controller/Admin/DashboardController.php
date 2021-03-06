<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
* @Route("/admin/dashboard", name="admin_dashboard_")
*/
class DashboardController extends AdminBaseController
{
    /**
     * @Route("/", name="dashboard")
     * @return Response
     */
    public function dashboard()
    {
      return $this->render('admin/dashboard.html.twig', [
      ]);
    }
}
