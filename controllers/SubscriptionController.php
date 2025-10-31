<?php

declare(strict_types=1);

namespace app\controllers;

use app\domain\Subscription\Commands\CreateSubscriptionCommand;
use app\domain\Subscription\ValueObjects\PhoneNumber;
use app\dto\Subscription\CreateSubscriptionDTO;
use app\queries\Subscription\GetSubscriptionsByPhoneQuery;
use app\services\Author\GetAuthorsActiveRecordsHandler;
use app\services\Subscription\CreateSubscriptionHandler;
use app\services\Subscription\GetSubscriptionsByPhoneHandler;
use app\services\Subscription\GetSubscriptionsForViewHandler;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

final class SubscriptionController extends BaseController
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'view'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $phone = Yii::$app->request->get('phone', '');
        $subscriptions = [];

        if ($phone !== '') {
            try {
                $handler = $this->getHandler(GetSubscriptionsByPhoneHandler::class);
                $phoneNumber = new PhoneNumber($phone);
                $query = new GetSubscriptionsByPhoneQuery($phoneNumber);
                $subscriptions = $handler->handle($query);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', 'Ошибка при загрузке подписок: ' . $e->getMessage());
            }
        }

        return $this->render('index', [
            'subscriptions' => $subscriptions,
            'phone' => $phone,
        ]);
    }

    public function actionCreate(): Response|string
    {
        $model = new \app\models\SubscriptionForm();
        $prefillAuthorId = (int) Yii::$app->request->get('author_id', 0);
        if ($prefillAuthorId > 0) {
            $model->author_id = $prefillAuthorId;
        }

        if (Yii::$app->request->isPost) {
            try {
                $handler = $this->getHandler(CreateSubscriptionHandler::class);

                $postData = Yii::$app->request->post();
                if (isset($postData['SubscriptionForm'])) {
                    $dto = CreateSubscriptionDTO::fromArray($postData['SubscriptionForm']);
                } else {
                    $dto = CreateSubscriptionDTO::fromArray($postData);
                }
                $command = new CreateSubscriptionCommand($dto);

                $subscription = $handler->handle($command);

                Yii::$app->session->setFlash('success', 'Подписка успешно создана!');

                return $this->redirect(['view', 'phone' => $subscription->getPhoneNumber()->getValue()]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', 'Ошибка при создании подписки: ' . $e->getMessage());
            }
        }

        try {
            $handler = $this->getHandler(GetAuthorsActiveRecordsHandler::class);
            $authors = $handler->getAll();
        } catch (Exception $e) {
            $authors = [];
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    public function actionView(): Response|string
    {
        $phone = Yii::$app->request->get('phone', '');

        if ($phone === '') {
            Yii::$app->session->setFlash('error', 'Укажите номер телефона');

            return $this->redirect(['index']);
        }

        $subscriptions = [];

        try {
            $handler = $this->getHandler(GetSubscriptionsForViewHandler::class);
            $phoneNumber = new PhoneNumber($phone);
            $subscriptions = $handler->handle($phoneNumber);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка при загрузке подписок: ' . $e->getMessage());
        }

        return $this->render('view', [
            'subscriptions' => $subscriptions,
            'phone' => $phone,
        ]);
    }

    public function actionUnsubscribe(int $id): Response
    {
        try {
            Yii::$app->session->setFlash('success', 'Вы успешно отписались от уведомлений!');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка при отписке: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }
}
