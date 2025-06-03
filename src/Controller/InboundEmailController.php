<?php

namespace App\Controller;

use App\Entity\InboundEmail;
use App\Repository\InboundEmailRepository;
use App\Service\InboundMailerService;
use App\Service\OpenAIService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inbound-email')]
final class InboundEmailController extends AbstractController
{
    const ACTIVE_PAGE = 'inbound_email';

    public function __construct(
        private readonly InboundMailerService $inboundMailerService,
        private readonly OpenAIService        $openAIService,
    )
    {
    }

    #[Route(name: 'app_inbound_email_index', methods: ['GET'])]
    public function index(InboundEmailRepository $inboundEmailRepository): Response
    {
        return $this->render('inbound_email/index.html.twig', [
            'active' => self::ACTIVE_PAGE,
            'inbound_emails' => $inboundEmailRepository->findBy([], ['dateReceived' => 'DESC']),
        ]);
    }

    #[Route('/receive', name: 'app_inbound_email_receive', methods: ['POST'])]
    public function receive(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        // Validate the request content
        $isRequestValid = $this->inboundMailerService->validatePayload($payload);
        if (!$isRequestValid) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid request content'
            ], Response::HTTP_BAD_REQUEST);
        }

        $inboundEmail = $this->inboundMailerService->createInboundEmailFromPayload($payload);

        // Call OpenAI to extract purchase JSON from the email body
        $aiResponse = $this->openAIService->extractPurchaseJsonFromEmail($inboundEmail->getHtmlBody());
        if (isset($purchaseJson['error'])) {
            return new JsonResponse($purchaseJson, Response::HTTP_BAD_REQUEST);
        } else {
            // Check if the AI response contains the expected JSON format
            if (isset($aiResponse['output'][0]['content'][0]['text'])) {
                $purchaseJson = json_decode($aiResponse['output'][0]['content'][0]['text'], true);
                $upsertResponse = $this->inboundMailerService->upsertPurchaseFromInboundEmailJson($purchaseJson, $inboundEmail);
                return new JsonResponse($upsertResponse, $upsertResponse ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);

            } else {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Invalid AI response format'
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    #[Route('/{id}', name: 'app_inbound_email_show', methods: ['GET'])]
    public function show(InboundEmail $inboundEmail): Response
    {
        return $this->render('inbound_email/show.html.twig', [
            'active' => self::ACTIVE_PAGE,
            'inbound_email' => $inboundEmail,
        ]);
    }

    #[Route('/{id}', name: 'app_inbound_email_delete', methods: ['POST'])]
    public function delete(Request $request, InboundEmail $inboundEmail, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inboundEmail->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($inboundEmail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_inbound_email_index', [], Response::HTTP_SEE_OTHER);
    }
}
