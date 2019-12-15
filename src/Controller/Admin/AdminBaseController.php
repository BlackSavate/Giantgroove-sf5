<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
* @Route("/admin", name="admin_")
* @IsGranted("ROLE_ADMIN")
*/
abstract class AdminBaseController extends AbstractController
{
}
