<?php

declare(strict_types=1);

namespace app\controllers;

use Exception;
use Throwable;
use Yii;
use yii\web\Controller;

abstract class BaseController extends Controller
{
    protected function getHandler(string $className): object
    {
        return Yii::$container->get($className);
    }

    protected function getService(string $className): object
    {
        return Yii::$container->get($className);
    }

    protected function handleDomainException(Throwable $exception): void
    {
        $handler = $this->getService(\app\services\Common\ExceptionHandler::class);
        $handler->handle($exception);
    }

    protected function handlePostRequest(string $handlerClass, string $dtoClass, string $commandClass, string $successMessage = 'Operation completed successfully!'): mixed
    {
        if (Yii::$app->request->isPost === false) {
            return false;
        }

        try {
            $handler = $this->getHandler($handlerClass);
            $postData = Yii::$app->request->post();

            $dto = $dtoClass::fromArray($postData);
            $command = new $commandClass($dto);
            $result = $handler->handle($command);

            Yii::$app->session->setFlash('success', $successMessage);

            return $result;
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());

            throw $e;
        }
    }
}
