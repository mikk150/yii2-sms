<?php

namespace yiiunit;

use yiiunit\implementations\Message;
use yiiunit\implementations\Provider;

/**
 *
 */
class BaseProviderTest extends TestCase
{
    public function testCompose()
    {
        $provider = new Provider;

        $message = $provider->compose()->setBody('this is test SMS');

        $this->assertInstanceOf('mikk150\sms\MessageInterface', $message);
        $this->assertEquals('this is test SMS', $message->getBody());
    }

    public function testComposeWithText()
    {
        $provider = new Provider;

        $message = $provider->compose('this is test SMS');

        $this->assertInstanceOf('mikk150\sms\MessageInterface', $message);
        $this->assertEquals('this is test SMS', $message->getBody());
    }

    public function testComposeWithParams()
    {
        $provider = new Provider;

        $message = $provider->compose('this is test SMS with content {content}', [
            'content' => 'Test'
        ]);

        $this->assertInstanceOf('mikk150\sms\MessageInterface', $message);
        $this->assertEquals('this is test SMS with content Test', $message->getBody());
    }

    public function testSend()
    {
        $providerMock = $this->getMockBuilder(Provider::className())->setMethods(['sendMessage'])->getMock();

        $message = $providerMock->compose('this is test SMS with content {content}', [
            'content' => 'Test'
        ]);

        $providerMock->expects($this->once())->method('sendMessage')->will($this->returnValue(true));

        $providerMock->compose('test')->send();
    }

    public function testSendToMultipleRecepients()
    {
        $providerMock = $this->getMockBuilder(Provider::className())->setMethods(['sendMessage'])->getMock();

        $message = $providerMock->compose('this is test SMS with content {content}', [
            'content' => 'Test'
        ]);

        $providerMock->expects($this->once())->method('sendMessage')->will($this->returnValue(true));

        $providerMock->compose('test')->setTo([123, 223])->send();
    }

    public function testSendMultipleMessages()
    {
        $providerMock = $this->getMockBuilder(Provider::className())->setMethods(['sendMessage'])->getMock();

        $messages = [];

        $messages[] = $providerMock->compose('this is test SMS with content {content}', [
            'content' => 'Test'
        ]);

        $messages[] = $providerMock->compose('this is test SMS with content {content}', [
            'content' => 'Test'
        ]);

        $providerMock->expects($this->exactly(2))->method('sendMessage')->withConsecutive(
            [$messages[0]],
            [$messages[1]]
        )->will($this->returnValue(true));

        $count = $providerMock->sendMultiple($messages);

        $this->assertEquals(2, $count);
    }

    public function testBeforeSendReturningTrueDoesNotSend()
    {
        $providerMock = $this->getMockBuilder(Provider::className())->setMethods(['sendMessage', 'beforeSend'])->getMock();

        $providerMock->expects($this->once())->method('beforeSend')->will($this->returnValue(false));
        $providerMock->expects($this->never())->method('sendMessage')->will($this->returnValue(false));

        $providerMock->compose('test')->send();
    }

    public function testFileTransport()
    {
        $provider = new Provider([
            'useFileTransport' => true
        ]);

        $provider->compose('this is test SMS')->send();
    }
}
