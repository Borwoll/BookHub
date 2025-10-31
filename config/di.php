<?php

declare(strict_types=1);

return static function (): void {
    $container = Yii::$container;
    $container->setSingleton(
        'app\domain\Author\Repositories\AuthorRepositoryInterface',
        'app\repositories\Author\AuthorRepository',
    );
    $container->setSingleton(
        'app\domain\Book\Repositories\BookRepositoryInterface',
        'app\repositories\Book\BookRepository',
    );
    $container->setSingleton(
        'app\domain\Subscription\Repositories\SubscriptionRepositoryInterface',
        'app\repositories\Subscription\SubscriptionRepository',
    );

    $container->setSingleton('app\domain\Book\Services\BookDomainService');
    $container->setSingleton('app\domain\Report\Services\ReportDomainService');

    $container->setSingleton('app\services\Book\BookCreationService');
    $container->setSingleton('app\strategies\Book\StandardBookCreationStrategy');

    $container->setSingleton('app\services\Common\SmsService');
    $container->setSingleton('app\services\Common\ExceptionHandler');

    $container->setSingleton('app\services\Book\BookCreatedEventHandler');
    $container->setSingleton('app\services\Book\CreateBookHandler');
    $container->setSingleton('app\services\Book\GetBookHandler');
    $container->setSingleton('app\services\Book\GetBooksHandler');
    $container->setSingleton('app\services\Book\GetBookViewModelHandler');

    $container->setSingleton('app\services\Author\CreateAuthorHandler');
    $container->setSingleton('app\services\Author\GetAuthorHandler');
    $container->setSingleton('app\services\Author\GetAllAuthorsHandler');
    $container->setSingleton('app\services\Author\GetAuthorViewDataHandler');

    $container->setSingleton('app\services\Subscription\CreateSubscriptionHandler');

    $container->setSingleton('app\services\Report\GetTopAuthorsHandler');
};
