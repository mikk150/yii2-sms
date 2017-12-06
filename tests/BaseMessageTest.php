<?php

namespace yiiunit;

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
}
