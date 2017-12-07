<?php

namespace yiiunit;

use yiiunit\implementations\Message;
use yiiunit\implementations\Provider;
use Yii;

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

    public function testMessageFileNameGeneratorReturnsString()
    {
        $provider = new Provider;

        $this->assertNotEmpty($provider->generateMessageFileName());
    }

    public function testFileTransport()
    {
        $providerMock = $this->getMockBuilder(Provider::className())->setConstructorArgs([[
            'useFileTransport' => true
        ]])->setMethods(['generateMessageFileName'])->getMock();

        $providerMock->expects($this->once())->method('generateMessageFileName')->will($this->returnValue('testfilename1.txt'));

        $message = $providerMock->compose('this is test SMS');

        $this->assertTrue($message->send());

        $file = Yii::getAlias($providerMock->fileTransportPath) . '/testfilename1.txt';

        $this->assertStringEqualsFile($file, $message->toString());
    }

    public function testFileTransportWithCustomName()
    {
        $provider = new Provider([
            'useFileTransport' => true,
            'fileTransportCallback' => function () {
                return 'testfilename2.txt';
            }
        ]);

        $message = $provider->compose('this is test SMS');

        $this->assertTrue($message->send());

        $file = Yii::getAlias($provider->fileTransportPath) . '/testfilename2.txt';

        $this->assertStringEqualsFile($file, $message->toString());
    }
}
