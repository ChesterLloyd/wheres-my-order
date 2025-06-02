<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Form\PurchaseForm;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
final class PurchaseController extends AbstractController
{
    const ACTIVE_PAGE = 'purchase';

    #[Route(name: 'app_purchase_index', methods: ['GET'])]
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        return $this->render('purchase/index.html.twig', [
            'active' => self::ACTIVE_PAGE,
            'purchases' => $purchaseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_purchase_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $purchase = new Purchase();
        $form = $this->createForm(PurchaseForm::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($purchase);
            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('purchase/new.html.twig', [
            'active' => self::ACTIVE_PAGE,
            'purchase' => $purchase,
            'form' => $form,
            'new' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_show', methods: ['GET'])]
    public function show(Purchase $purchase): Response
    {
        return $this->render('purchase/show.html.twig', [
            'active' => self::ACTIVE_PAGE,
            'purchase' => $purchase,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_purchase_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Purchase $purchase, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PurchaseForm::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_show', ['id' => $purchase->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('purchase/edit.html.twig', [
            'active' => self::ACTIVE_PAGE,
            'purchase' => $purchase,
            'form' => $form,
            'new' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_delete', methods: ['POST'])]
    public function delete(Request $request, Purchase $purchase, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$purchase->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($purchase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
    }
}
