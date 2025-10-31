<?php

declare(strict_types=1);

namespace app\services\Report;

use app\domain\Report\Services\ReportDomainService;
use app\queries\Report\GetTopAuthorsQuery;

final class GetTopAuthorsHandler
{
    public function __construct(
        private readonly ReportDomainService $reportService,
    ) {}

    public function handle(GetTopAuthorsQuery $query): array
    {
        return $this->reportService->generateTopAuthorsByYear($query->year, $query->limit);
    }
}
