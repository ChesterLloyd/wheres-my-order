<?php

namespace App\Service;

use Postmark\Models\PostmarkException;
use Postmark\PostmarkClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MailerService
{
    private string $mailToken;
    static string $STREAM_RESET_PASSWORD = 'password-reset';
    static string $ERROR_GENERIC = 'An error occurred whilst sending the email. Please try again or use an alternative method.';

    public function __construct(
        #[Autowire(env: 'MAIL_TOKEN')]
        string                           $mailToken,
        private readonly LoggerInterface $logger,
    )
    {
        $this->mailToken = $mailToken;
    }

    /**
     * Sends an email with the given parameters.
     * Returns true if the email delivery was successful.
     *
     * @param string $recipient
     * @param string $subject
     * @param string $body
     * @param string $stream
     * @return bool
     */
    public function sendEmail(string $recipient, string $subject, string $body, string $stream): bool
    {
        $client = new PostmarkClient($this->mailToken);

        try {
            $client->sendEmail(
                "notifications@wheres-my-order.com",
                $recipient,
                $subject,
                $body,
                '',
                null,
                false,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $stream,
            );

            return true;
        } catch (PostmarkException $e) {
            $this->logger->error('Error sending email: ' . $e->getMessage());
        }

        return false;
    }
}
