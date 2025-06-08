<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Form\SettingsForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $ongoingOrders = $entityManager->getRepository(Purchase::class)
            ->findRecentOngoingPurchasesByUserId($this->getUser()->getId());

        return $this->render('dashboard/index.html.twig', [
            'active' => 'dashboard',
            'ongoingOrders' => $ongoingOrders,
        ]);
    }

    #[Route('/settings', name: 'app_settings', methods: ['GET', 'POST'])]
    public function settings(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(SettingsForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your settings have been saved.');
            return $this->redirectToRoute('app_settings', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/settings.html.twig', [
            'active' => 'settings',
            'form' => $form,
            'new' => true,
        ]);
    }
}
