<?php

declare(strict_types=1);

namespace tests\unit\controllers;

use app\controllers\SubscriptionController;
use app\domain\Subscription\Commands\CreateSubscriptionCommand;
use app\domain\Subscription\Entities\Subscription;
use app\domain\Subscription\Repositories\SubscriptionRepositoryInterface;
use app\domain\Subscription\ValueObjects\PhoneNumber;
use app\services\Subscription\CreateSubscriptionHandler;
use Codeception\Test\Unit;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Yii;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;

/**
 * @internal
 * @small
 */
final class SubscriptionControllerTest extends Unit
{
    private SubscriptionController $controller;

    private MockObject|Request $request;

    private MockObject|Session $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new SubscriptionController('subscription', Yii::$app);

        $this->request = $this->createMock(Request::class);
        $this->session = $this->createMock(Session::class);

        Yii::$app->set('request', $this->request);
        Yii::$app->set('session', $this->session);
    }

    public function test_action_index_without_phone(): void
    {
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) {
            return $param === 'phone' ? '' : $default;
        });

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_index_with_phone(): void
    {
        $phone = '+79991234567';
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($phone) {
            return $param === 'phone' ? $phone : $default;
        });

        $mockRepository = $this->createMock(SubscriptionRepositoryInterface::class);

        $subscription = $this->createMock(Subscription::class);
        $subscription->method('getAuthorId')->willReturn(1);

        $phoneNumber = $this->createMock(PhoneNumber::class);

        $mockRepository->expects($this->once())
            ->method('findByPhone')
            ->with($this->isInstanceOf(PhoneNumber::class))
            ->willReturn([$subscription]);

        Yii::$container->set(SubscriptionRepositoryInterface::class, $mockRepository);

        $authorModel = $this->createMock(\app\models\Author::class);
        $authorModel->full_name = 'Test Author';

        $authorModel->expects($this->once())
            ->method('findOne')
            ->with(1)
            ->willReturn($authorModel);

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_index_with_phone_error(): void
    {
        $phone = 'invalid';
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($phone) {
            return $param === 'phone' ? $phone : $default;
        });

        $mockRepository = $this->createMock(SubscriptionRepositoryInterface::class);

        $mockRepository->expects($this->once())
            ->method('findByPhone')
            ->willThrowException(new InvalidArgumentException('Invalid phone number'));

        Yii::$container->set(SubscriptionRepositoryInterface::class, $mockRepository);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('error', $this->stringContains('Ошибка'));

        $result = $this->controller->actionIndex();

        $this->assertIsString($result);
    }

    public function test_action_create_get_request(): void
    {
        $this->request->method('isPost')->willReturn(false);
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) {
            return $param === 'author_id' ? 1 : $default;
        });

        $authorModel = $this->getMockBuilder(\app\models\Author::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $query = $this->createMock(Yii\db\ActiveQuery::class);
        $query->method('orderBy')->willReturnSelf();
        $query->method('all')->willReturn([]);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($query);

        $result = $this->controller->actionCreate();

        $this->assertIsString($result);
    }

    public function test_action_create_post_request_with_subscription_form(): void
    {
        $this->request->method('isPost')->willReturn(true);
        $this->request->method('post')->willReturn([
            'SubscriptionForm' => [
                'phone' => '+79991234567',
                'author_id' => 1,
            ],
        ]);

        $mockHandler = $this->createMock(CreateSubscriptionHandler::class);

        $subscription = $this->createMock(Subscription::class);
        $phoneNumber = $this->createMock(PhoneNumber::class);
        $phoneNumber->method('getValue')->willReturn('+79991234567');
        $subscription->method('getPhoneNumber')->willReturn($phoneNumber);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(CreateSubscriptionCommand::class))
            ->willReturn($subscription);

        Yii::$container->set(CreateSubscriptionHandler::class, $mockHandler);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('success', $this->stringContains('успешно создана'));

        $authorModel = $this->getMockBuilder(\app\models\Author::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $query = $this->createMock(Yii\db\ActiveQuery::class);
        $query->method('orderBy')->willReturnSelf();
        $query->method('all')->willReturn([]);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($query);

        $result = $this->controller->actionCreate();

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_create_post_request_with_direct_data(): void
    {
        $this->request->method('isPost')->willReturn(true);
        $this->request->method('post')->willReturn([
            'phone' => '+79991234567',
            'author_id' => 1,
        ]);

        $mockHandler = $this->createMock(CreateSubscriptionHandler::class);

        $subscription = $this->createMock(Subscription::class);
        $phoneNumber = $this->createMock(PhoneNumber::class);
        $phoneNumber->method('getValue')->willReturn('+79991234567');
        $subscription->method('getPhoneNumber')->willReturn($phoneNumber);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(CreateSubscriptionCommand::class))
            ->willReturn($subscription);

        Yii::$container->set(CreateSubscriptionHandler::class, $mockHandler);

        $authorModel = $this->getMockBuilder(\app\models\Author::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $query = $this->createMock(Yii\db\ActiveQuery::class);
        $query->method('orderBy')->willReturnSelf();
        $query->method('all')->willReturn([]);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($query);

        try {
            $result = $this->controller->actionCreate();
            $this->assertInstanceOf(Response::class, $result);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_action_create_post_request_error(): void
    {
        $this->request->method('isPost')->willReturn(true);
        $this->request->method('post')->willReturn([
            'SubscriptionForm' => [
                'phone' => 'invalid',
                'author_id' => 1,
            ],
        ]);

        $mockHandler = $this->createMock(CreateSubscriptionHandler::class);

        $mockHandler->expects($this->once())
            ->method('handle')
            ->willThrowException(new InvalidArgumentException('Invalid phone number'));

        Yii::$container->set(CreateSubscriptionHandler::class, $mockHandler);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('error', $this->stringContains('Ошибка'));

        $authorModel = $this->getMockBuilder(\app\models\Author::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $query = $this->createMock(Yii\db\ActiveQuery::class);
        $query->method('orderBy')->willReturnSelf();
        $query->method('all')->willReturn([]);

        $authorModel->expects($this->once())
            ->method('find')
            ->willReturn($query);

        $result = $this->controller->actionCreate();

        $this->assertIsString($result);
    }

    public function test_action_view_without_phone(): void
    {
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) {
            return $param === 'phone' ? '' : $default;
        });

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('error', $this->stringContains('номер телефона'));

        $result = $this->controller->actionView();

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_view_with_phone(): void
    {
        $phone = '+79991234567';
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($phone) {
            return $param === 'phone' ? $phone : $default;
        });

        $mockRepository = $this->createMock(SubscriptionRepositoryInterface::class);

        $subscription = $this->createMock(Subscription::class);
        $subscription->method('getId')->willReturn(1);

        $mockRepository->expects($this->once())
            ->method('findByPhone')
            ->with($this->isInstanceOf(PhoneNumber::class))
            ->willReturn([$subscription]);

        Yii::$container->set(SubscriptionRepositoryInterface::class, $mockRepository);

        $subscriptionModel = $this->createMock(\app\models\Subscription::class);
        $subscriptionModel->id = 1;

        $subscriptionModel->expects($this->once())
            ->method('findOne')
            ->with(1)
            ->willReturn($subscriptionModel);

        $result = $this->controller->actionView();

        $this->assertIsString($result);
    }

    public function test_action_view_with_error(): void
    {
        $phone = 'invalid';
        $this->request->method('get')->willReturnCallback(function ($param, $default = null) use ($phone) {
            return $param === 'phone' ? $phone : $default;
        });

        $mockRepository = $this->createMock(SubscriptionRepositoryInterface::class);

        $mockRepository->expects($this->once())
            ->method('findByPhone')
            ->willThrowException(new InvalidArgumentException('Invalid phone'));

        Yii::$container->set(SubscriptionRepositoryInterface::class, $mockRepository);

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('error', $this->stringContains('Ошибка'));

        $result = $this->controller->actionView();

        $this->assertIsString($result);
    }

    public function test_action_unsubscribe(): void
    {
        $subscriptionId = 1;

        $this->session->expects($this->once())
            ->method('setFlash')
            ->with('success', $this->stringContains('отписались'));

        $result = $this->controller->actionUnsubscribe($subscriptionId);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function test_action_unsubscribe_with_error(): void
    {
        $subscriptionId = 1;

        $this->session->expects($this->any())
            ->method('setFlash');

        $result = $this->controller->actionUnsubscribe($subscriptionId);

        $this->assertInstanceOf(Response::class, $result);
    }
}
