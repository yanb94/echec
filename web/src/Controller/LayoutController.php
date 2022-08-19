<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LayoutController extends AbstractController
{
    public function headerSpaceMember(): Response
    {
        $response = $this->render('layout/header_space_member.html.twig', [])
            ->setPrivate()
            ->setSharedMaxAge(0)
        ;

        return $response;
    }

    public function drawerSpaceMember(): Response
    {
        $response = $this->render('layout/drawer_space_member.html.twig', [])
            ->setPrivate()
            ->setSharedMaxAge(0)
        ;

        return $response;
    }
}
