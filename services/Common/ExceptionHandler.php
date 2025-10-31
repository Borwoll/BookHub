<?php

declare(strict_types=1);

namespace app\services\Common;

use app\domain\Book\Exceptions\BookNotFoundException;
use app\domain\Common\DomainException;
use InvalidArgumentException;
use Throwable;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

final class ExceptionHandler
{
    public function handle(Throwable $exception): void
    {
        $this->logException($exception);
        $this->convertAndThrow($exception);
    }

    private function logException(Throwable $exception): void
    {
        $context = [
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'request_uri' => Yii::$app->request->getUrl(),
            'user_ip' => Yii::$app->request->getUserIP(),
        ];

        $contextString = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($exception instanceof DomainException) {
            Yii::warning('Domain exception occurred: ' . $contextString, __METHOD__);
        } else {
            Yii::error('Unexpected exception occurred: ' . $contextString, __METHOD__);
        }
    }

    private function convertAndThrow(Throwable $exception): void
    {
        match (true) {
            $exception instanceof BookNotFoundException
                => throw new NotFoundHttpException($exception->getMessage(), 0, $exception),

            $exception instanceof InvalidArgumentException
                => throw new BadRequestHttpException($exception->getMessage(), 0, $exception),

            $exception instanceof DomainException
                => throw new BadRequestHttpException('Business logic error: ' . $exception->getMessage(), 0, $exception),

            default => throw $exception,
        };
    }
}
