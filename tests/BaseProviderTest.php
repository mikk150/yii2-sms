<?php

namespace yiiunit;

/**
 *
 */
class BaseProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testCompose()
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
        $providerMock = $this->getMockBuilder(Provider::className())->setMethods(['send'])->getMock();

        $providerMock->expects($this->once())->method('send')->will($this->returnValue(true));

        $providerMock->compose('test')->send();
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
    }
    public function getBody()
    {
        return $this->_body;
    }
    public function setBody($body)
    {
        $this->_body = $body;
    }
    public function getTo()
    {
        return $this->_to;
    }
    public function setTo($to)
    {
        $this->_to = $to;
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
