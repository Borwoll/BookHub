<?php

declare(strict_types=1);

namespace app\viewModels\Report;

use app\dto\Report\TopAuthorsReportDTO;

final readonly class ReportViewModel
{
    public function __construct(
        public int $year,
        public array $topAuthors,
        public array $availableYears,
    ) {}

    public static function fromReportData(int $year, array $topAuthors, array $availableYears): self
    {
        return new self($year, $topAuthors, $availableYears);
    }

    public function getTopAuthorsAsArray(): array
    {
        return array_map(
            fn(TopAuthorsReportDTO $dto) => $dto->toArray(),
            $this->topAuthors,
        );
    }

    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'top_authors' => $this->getTopAuthorsAsArray(),
            'available_years' => $this->availableYears,
        ];
    }
}
