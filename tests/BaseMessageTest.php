<?php

namespace yiiunit;

use yiiunit\implementations\Message;
use yiiunit\implementations\Provider;

/**
 *
 */
class BaseMessageTest extends TestCase
{
    public function testSend()
    {
        $providerMock = $this->getMockBuilder(Provider::className())->setMethods(['send'])->getMock();

        $providerMock->expects($this->once())->method('send')->will($this->returnValue(true));

        $providerMock->compose('test')->send();
    }

    public function testToString()
    {
        
    }
}
