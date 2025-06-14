<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/item')]
final class ItemController extends AbstractController
{
    const ACTIVE_PAGE = 'purchase';

    #[Route('/{id}/edit', name: 'app_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item, EntityManagerInterface $entityManager): Response
    {
        if ($item->getPurchase()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have permission to edit this item.');
        }

        $form = $this->createForm(ItemForm::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_show', ['id' => $item->getPurchase()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('item/edit.html.twig', [
            'active' => self::ACTIVE_PAGE,
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_item_delete', methods: ['POST'])]
    public function delete(Request $request, Item $item, EntityManagerInterface $entityManager): Response
    {
        if ($item->getPurchase()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have permission to delete this item.');
        }

        $purchaseId = $item->getPurchase()->getId();
        if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_purchase_show', ['id' => $purchaseId], Response::HTTP_SEE_OTHER);
    }
}
