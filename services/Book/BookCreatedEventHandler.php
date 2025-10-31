<?php

declare(strict_types=1);

namespace app\services\Book;

use app\domain\Book\Events\BookCreated;
use app\services\Common\SmsService;
use Exception;
use Psr\Log\LoggerInterface;

final class BookCreatedEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SmsService $smsService,
    ) {}

    public function handle(BookCreated $event): void
    {
        $payload = $event->getPayload();
        $book = $event->getBook();

        $this->logger->info('New book created', [
            'book_id' => $payload['book_id'],
            'title' => $payload['title'],
            'year' => $payload['year'],
            'authors_count' => count($payload['authors'] ?? []),
            'occurred_at' => $event->getOccurredAt()->format('Y-m-d H:i:s'),
        ]);

        $authorIds = $payload['authors'] ?? [];
        $bookTitle = $payload['title'] ?? '';

        foreach ($authorIds as $authorId) {
            try {
                $this->smsService->notifySubscribersAboutNewBook($authorId, $bookTitle);
            } catch (Exception $e) {
                $this->logger->error('Failed to send SMS notifications for new book', [
                    'book_id' => $payload['book_id'],
                    'author_id' => $authorId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
