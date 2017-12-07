<?php

namespace mikk150\sms;

use yii\base\BaseObject;
use yii\base\ErrorHandler;
use Yii;

/**
 *
 */
abstract class BaseMessage extends BaseObject implements MessageInterface
{

    /**
     * @var Provider the provider instance that created this message.
     * For independently created messages this is `null`.
     */
    public $provider;

    /**
     * Sends this SMS message.
     * @param ProviderInterface $provider the provider that should be used to send this message.
     * If no provider is given it will first check if [[provider]] is set and if not,
     * the "sms" application component will be used instead.
     * @return bool whether this message is sent successfully.
     */
    public function send(ProviderInterface $provider = null)
    {
        if ($provider === null && $this->provider === null) {
            $provider = Yii::$app->get('sms');
        } elseif ($provider === null) {
            $provider = $this->provider;
        }

        return $provider->send($this);
    }

    /**
     * PHP magic method that returns the string representation of this object.
     * @return string the string representation of this object.
     */
    public function __toString()
    {
        // __toString cannot throw exception
        // use trigger_error to bypass this limitation
        try {
            return $this->toString();
        } catch (\Exception $e) {
            ErrorHandler::convertExceptionToError($e);
            return '';
        }
    }
}
