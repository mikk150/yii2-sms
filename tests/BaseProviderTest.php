<?php

namespace yiiunit;

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

    public function test($value='')
    {
        # code...
    }
}

/**
 * Test Provider class.
 */
class Provider extends \mikk150\sms\BaseProvider
{
    public $messageClass = 'yiiunit\Message';
    
    public $sentMessages = [];
    
    protected function sendMessage($message)
    {
        $this->sentMessages[] = $message;
        return true;
    }
}

/**
 * Test Message class.
 */
class Message extends \mikk150\sms\BaseMessage
{
    private $_from;
    private $_body;
    private $_to;

    public function getFrom()
    {
        return $this->_from;
    }
    public function setFrom($from)
    {
        $this->_from = $from;

        return $this;
    }
    public function getBody()
    {
        return $this->_body;
    }
    public function setBody($body)
    {
        $this->_body = $body;

        return $this;
    }
    public function getTo()
    {
        return $this->_to;
    }
    public function setTo($to)
    {
        $this->_to = $to;

        return $this;
    }

    public function toString()
    {
        $provider = $this->provider;
        $this->provider = null;
        $s = var_export($this, true);
        $this->provider = $provider;
        return $s;
    }
}
