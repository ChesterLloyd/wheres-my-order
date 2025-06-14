<?php

namespace App\Service;

use App\Entity\InboundEmail;
use App\Entity\Item;
use App\Entity\Purchase;
use App\Entity\Store;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

readonly class InboundMailerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface        $logger,
        private MailerService          $mailerService,
        #[Autowire(env: 'MAIL_INBOUND_DOMAIN')]
        private string                 $inboundEmailDomain,
    )
    {
    }

    /**
     * Generate a unique inbound email address for a user.
     *
     * @return string
     */
    public function generateInboundEmailAddress(): string
    {
        return Uuid::v4() . '@' . $this->inboundEmailDomain;
    }

    /**
     * Validate the payload of the inbound email.
     *
     * @param array $payload
     * @return bool
     */
    public function validatePayload(array $payload): bool
    {
        $validator = Validation::createValidator();
        $violationResult = $validator->validate($payload, [
            new Collection([
                'fields' => [
                    'From' => [
                        new NotBlank(),
                        new Email(),
                    ],
                    'To' => [
                        new NotBlank(),
                        new Email(),
                    ],
                    'Subject' => [
                        new NotBlank(),
                    ],
                    'HtmlBody' => [
                        new NotBlank(),
                    ],
                    'TextBody' => [
                        new NotBlank(),
                    ],
                ],
                'allowExtraFields' => true,
            ]),
        ]);

        return $violationResult->count() == 0;
    }

    /**
     * Create an InboundEmail entity from the provided payload.
     *
     * @param array $payload
     * @return InboundEmail
     */
    public function createInboundEmailFromPayload(array $payload): InboundEmail
    {
        $inboundEmail = new InboundEmail();
        $inboundEmail->setDateReceived(new DateTime())
            ->setSender($payload['From'])
            ->setRecipient($payload['To'])
            ->setSubject($payload['Subject'])
            ->setHtmlBody($payload['HtmlBody'])
            ->setTextBody($payload['TextBody'])
            ->setStatus('new');

        $this->entityManager->persist($inboundEmail);
        $this->entityManager->flush();

        return $inboundEmail;
    }

    /**
     * Forward an InboundEmail to the user's account email
     * if they have opted in to forward emails.
     *
     * @param InboundEmail $inboundEmail
     * @return bool
     */
    public function forwardInboundEmail(InboundEmail $inboundEmail): bool
    {
        $this->logger->info(sprintf('Forwarding inbound email (inboundEmailId: %s)', $inboundEmail->getId()));

        // Find the user for this inbound email
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'inboundEmail' => $inboundEmail->getRecipient(),
        ]);
        if (!$user) {
            $this->logger->error(
                sprintf('User not found for inbound email (inboundEmailId: %s)', $inboundEmail->getId())
            );
            return false;
        }

        if ($user->isForwardEmails()) {
            $this->logger->info(sprintf('Forwarding email to user (userId: %s)', $user->getId()));

            // Forward the email to the user's email address
            $to = $user->getEmail();
            $subject = $inboundEmail->getSubject();
            $htmlBody = $inboundEmail->getHtmlBody();
            $textBody = $inboundEmail->getTextBody();

            return $this->mailerService->sendEmail($to, $subject, $htmlBody, $textBody, MailerService::$STREAM_FORWARD_EMAIL);
        } else {
            $this->logger->info(sprintf('User opted out of forwarding emails (userId: %s)', $user->getId()));
            return false;
        }
    }

    /**
     * Upsert a purchase from the provided JSON data and link it to the inbound email.
     *
     * @param array $purchaseJson
     * @param InboundEmail $inboundEmail
     * @return array
     * @throws \Exception
     */
    public function upsertPurchaseFromInboundEmailJson(array $purchaseJson, InboundEmail $inboundEmail): array
    {
        $this->logger->info(sprintf('Received email (inboundEmailId: %s)', $inboundEmail->getId()));
        $this->logger->debug(sprintf('Purchase JSON: %s', json_encode($purchaseJson)));

        // Find the user for this inbound email
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'inboundEmail' => $inboundEmail->getRecipient(),
        ]);
        if (!$user) {
            $inboundEmail->setStatus(InboundEmail::STATUS_FAILED)
                ->setStatusMessage(
                    sprintf('User not found for inbound email (inboundEmailId: %s)', $inboundEmail->getId())
                );
            $this->logger->error($inboundEmail->getStatusMessage());
            return [
                'status' => 'error',
                'message' => $inboundEmail->getStatusMessage(),
            ];
        }

        // Create a new purchase
        if ($purchaseJson['status'] === Purchase::STATUS_ACKNOWLEDGED) {
            $store = new Store();
            $store->setName($purchaseJson['store_name'] ?: 'Unknown')
                ->setWebsite($purchaseJson['store_website']);

            $purchase = new Purchase();
            $purchase->setUser($user)
                ->setStore($store)
                ->setOrderId($purchaseJson['order_id'])
                ->setStatus($purchaseJson['status'])
                ->setOrderDate(new DateTime($purchaseJson['purchase_date']))
                ->setAmount($purchaseJson['amount'] ?: 0.00)
                ->setCurrency($purchaseJson['currency'] ?: 'USD')
                ->setTrackingCourier($purchaseJson['tracking_courier'])
                ->setTrackingUrl($purchaseJson['tracking_url']);

            foreach ($purchaseJson['items'] as $itemJson) {
                $item = new Item();
                $item->setPurchase($purchase)
                    ->setName($itemJson['name'] ?: 'Unknown Item')
                    ->setDescription($itemJson['description'])
                    ->setAmount($itemJson['amount'])
                    ->setCurrency($itemJson['currency'])
                    ->setQuantity($itemJson['quantity']);
                $purchase->addItem($item);
            }

            $inboundEmail->setPurchase($purchase)
                ->setStatus(InboundEmail::STATUS_PROCESSED);
            $this->entityManager->persist($purchase);
            $this->entityManager->flush();

            return [
                'success' => 'success',
                'message' => sprintf('Successfully created purchase (purchaseId: %s)', $purchase->getId())
            ];

        } else {
            // Only update the status and link the email
            $purchase = $this->entityManager->getRepository(Purchase::class)->findOneBy([
                'user' => $user,
                'orderId' => $purchaseJson['order_id'],
            ]);
            if ($purchase) {
                $purchase->setStatus($purchaseJson['status']);
                $inboundEmail->setPurchase($purchase)
                    ->setStatus(InboundEmail::STATUS_PROCESSED);
                $this->entityManager->flush();

                return [
                    'success' => 'success',
                    'message' => sprintf('Successfully updated purchase (purchaseId: %s)', $purchase->getId())
                ];
            } else {
                $inboundEmail->setPurchase($purchase)
                    ->setStatus(InboundEmail::STATUS_FAILED)
                    ->setStatusMessage(sprintf('Failed to find the purchase (orderId: %s)', $purchaseJson['order_id']));
                $this->entityManager->flush();

                return [
                    'status' => 'error',
                    'message' => $inboundEmail->getStatusMessage(),
                ];
            }
        }
    }
}
