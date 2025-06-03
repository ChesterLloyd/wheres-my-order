<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class OpenAIService
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(env: 'OPENAI_API_KEY')]
        private string              $OPENAI_API_KEY,
    ) {
    }

    public function extractPurchaseJsonFromEmail(string $htmlBody): array
    {
        try {
            $response = $this->client->request(
                Request::METHOD_POST,
                'https://api.openai.com/v1/responses',
                [
                    'headers' => [
                        "Authorization: Bearer $this->OPENAI_API_KEY",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => 'gpt-4.1-nano',
                        'input' => [
                            [
                                'role' => 'system',
                                'content' => [
                                    [
                                        'type' => 'input_text',
                                        'text' => "Please take this email and return a JSON object of the key pieces. The email should be a delivery email (tracking, dispatch, acknowledgment, etc). The format of this JSON object should be: {\"store_name\":\"My Store LTD\",\"store_website\":\"https://example.com/store\",\"order_id\":\"1234\",\"status\":\"ACKNOWLEDGED|CANCELLED|DELIVERED|DISPATCHED|OUT FOR DELIVERY\",\"purchase_date\":\"YYYY-MM-DD HH:MM:SS\",\"amount\":0.00,\"currency\":\"USD\",\"tracking_courier\":\"CourierName\",\"tracking_url\":\"https://example.com\",\"items\":[{\"name\":\"item_name\",\"description\":\"item_description\",\"amount\":0.00,\"currency\":\"USD\",\"quantity\":0}]} .If a field has not been specified, return null."
                                    ]
                                ]
                            ],
                            [
                                'role' => 'user',
                                'content' => [
                                    [
                                        'type' => 'input_text',
                                        'text' => $htmlBody
                                    ]
                                ]
                            ]
                        ],
                        'text' => [
                            'format' => ['type' => 'json_object']
                        ],
                        'reasoning' => (object)[],
                        'tools' => [],
                        'temperature' => 1,
                        'max_output_tokens' => 2048,
                        'top_p' => 1,
                        'store' => false,
                    ]
                ]
            );
            $statusCode = $response->getStatusCode();

            if ($statusCode == Response::HTTP_OK) {
                return $response->toArray();

            } else {
                return [
                    'error' => 'Failed to fetch data from OpenAI API',
                    'message' => sprintf('%s - %s', $statusCode, $response->getContent(false)),
                ];
            }
        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            return [
                'error' => 'Failed to create purchase',
                'message' => $e->getMessage(),
            ];
        }
    }
}
