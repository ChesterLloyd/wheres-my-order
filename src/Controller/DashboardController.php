<?php

namespace App\Controller;

use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $ongoingOrders = $entityManager->getRepository(Purchase::class)
            ->findRecentOngoingPurchasesByUserId($this->getUser()->getId());
        $previousOrders = $entityManager->getRepository(Purchase::class)
            ->findRecentCompletedPurchasesByUserId($this->getUser()->getId());

        return $this->render('dashboard/index.html.twig', [
            'active' => 'dashboard',
            'ongoingOrders' => $ongoingOrders,
            'previousOrders' => $previousOrders,
        ]);
    }
}
