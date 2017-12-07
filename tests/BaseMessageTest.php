<?php

namespace yiiunit;

use yiiunit\implementations\Message;
use yiiunit\implementations\Provider;
use yii\base\ErrorHandler;
use Yii;

/**
 *
 */
class BaseMessageTest extends TestCase
{
    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'sms' => $this->createTestSmsComponent(),
            ],
        ]);
    }

    /**
     * @return Mailer test email component instance.
     */
    protected function createTestSmsComponent()
    {
        $component = new Provider();
        return $component;
    }

    public function testSend()
    {
        $providerMock = $this->getMockBuilder(Provider::className())->setMethods(['send'])->getMock();

        $providerMock->expects($this->once())->method('send')->will($this->returnValue(true));

        $providerMock->compose('test')->send();
    }

    public function testToString()
    {
        $message = new Message;

        $message->setTo('123456789');

        $this->assertEquals($message->toString(), (string) $message);
    }

    public function testSendingMessageWithoutProviderGetsDefaultProvider()
    {
        $providerMock = $this->getMockBuilder(Provider::className())->setMethods(['send'])->getMock();

        $providerMock->expects($this->once())->method('send')->will($this->returnValue(true));

        Yii::$app->setComponents([
            'sms' => $providerMock
        ]);

        $message = new Message;

        $message->send();


    }
}
