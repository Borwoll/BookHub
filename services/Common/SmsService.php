<?php

declare(strict_types=1);

namespace app\services\Common;

use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use Exception;
use Yii;
use yii\base\Component;
use yii\httpclient\Client;

final class SmsService extends Component
{
    public string $apiKey = '';

    public string $apiUrl = 'https://smspilot.ru/api.php';

    public string $sender = 'INFORM';

    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        array $config = [],
    ) {
        parent::__construct($config);

        if (isset(Yii::$app->params['sms'])) {
            $smsConfig = Yii::$app->params['sms'];
            $this->apiKey = $smsConfig['apiKey'] ?? '';
            $this->apiUrl = $smsConfig['apiUrl'] ?? 'https://smspilot.ru/api.php';
            $this->sender = $smsConfig['sender'] ?? 'INFORM';
        }
    }

    public function sendNewBookNotification(string $phone, string $authorName, string $bookTitle): bool
    {
        $message = "Новая книга от {$authorName}: \"{$bookTitle}\". Подробности на BookHub.";

        return $this->sendSms($phone, $message);
    }

    public function sendSms(string $phone, string $message): bool
    {
        try {
            $client = new Client();

            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl($this->apiUrl)
                ->setData([
                    'send' => $message,
                    'to' => $phone,
                    'from' => $this->sender,
                    'apikey' => $this->apiKey,
                    'format' => 'json',
                ])
                ->send();

            if ($response->isOk) {
                $data = $response->data;

                if (isset($data['send'])) {
                    foreach ($data['send'] as $sms) {
                        if ($sms['status'] === 'ok') {
                            Yii::info("SMS sent successfully to {$phone}: {$message}", __METHOD__);

                            return true;
                        }
                    }
                }

                Yii::warning("SMS sending failed for {$phone}. Response: " . json_encode($data), __METHOD__);

                return false;
            }
            Yii::error("SMS API request failed for {$phone}. HTTP Status: {$response->statusCode}", __METHOD__);

            return false;
        } catch (Exception $e) {
            Yii::error("SMS sending exception for {$phone}: {$e->getMessage()}", __METHOD__);

            return false;
        }
    }

    public function notifySubscribersAboutNewBook(int $authorId, string $bookTitle): array
    {
        $author = $this->authorRepository->findById($authorId);
        if ($author === null) {
            return ['success' => 0, 'failed' => 0, 'error' => 'Author not found'];
        }

        $subscriptions = $this->subscriptionRepository->findActiveByAuthor($author);

        $results = [
            'success' => 0,
            'failed' => 0,
            'total' => count($subscriptions),
        ];

        foreach ($subscriptions as $subscription) {
            if ($this->sendNewBookNotification(
                $subscription->getPhoneNumber()->getValue(),
                $author->getName()->getValue(),
                $bookTitle,
            )) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }
}
