<?php

declare(strict_types=1);

namespace app\domain\Report\Services;

use app\domain\Author\Repositories\AuthorRepositoryInterface;
use app\domain\Book\Repositories\BookRepositoryInterface;
use app\dto\Report\TopAuthorsReportDTO;

final class ReportDomainService
{
    public function __construct(
        private readonly AuthorRepositoryInterface $authorRepository,
        private readonly BookRepositoryInterface $bookRepository,
    ) {}

    public function generateTopAuthorsByYear(int $year, int $limit = 10): array
    {
        $topAuthorsData = $this->authorRepository->getTopAuthorsByYear($year, $limit);

        $reports = [];
        foreach ($topAuthorsData as $data) {
            $reports[] = new TopAuthorsReportDTO(
                (int) $data['id'],
                (string) $data['full_name'],
                (int) ($data['books_count'] ?? 0),
                0, // subscriptions_count можно добавить позже
            );
        }

        return $reports;
    }

    public function getAvailableYears(): array
    {
        $years = $this->bookRepository->getAvailableYears();
        $years = array_map('intval', $years);
        $years = array_values(array_unique($years));
        rsort($years);

        return $years;
    }
}
