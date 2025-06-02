<?php

namespace App\Service;

use App\Entity\InboundEmail;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
}
