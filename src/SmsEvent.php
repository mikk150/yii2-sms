<?php

namespace mikk150\sms;

use yii\base\Event;

/**
 * SmsEvent represents the event parameter used for events triggered by [[BaseProvider]].
 *
 * By setting the [[isValid]] property, one may control whether to continue running the action.
 *
 * @author Mikk Tendermann <mikk150@gmail.com>
 * @since 1.0
 */
class SmsEvent extends Event
{
    /**
     * @var MessageInterface the SMS message being send.
     */
    public $message;

    /**
     * @var bool if message was sent successfully.
     */
    public $isSuccessful;

    /**
     * @var bool whether to continue sending an sms. Event handlers of
     * [[\mikk150\sms\BaseProvider::EVENT_BEFORE_SEND]] may set this property to decide whether
     * to continue send or not.
     */
    public $isValid = true;
}
