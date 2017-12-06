<?php

namespace yiiunit\implementations;

/**
 * Test Provider class.
 */
class Provider extends \mikk150\sms\BaseProvider
{
    public $messageClass = 'yiiunit\implementations\Message';
    
    public $sentMessages = [];
    
    protected function sendMessage($message)
    {
        $this->sentMessages[] = $message;
        return true;
    }
}
