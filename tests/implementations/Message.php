<?php

namespace yiiunit\implementations;

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
