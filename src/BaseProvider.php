<?php

namespace mikk150\sms;

use yii\base\Component;

use Yii;

/**
 *
 */
abstract class BaseProvider extends Component implements ProviderInterface
{
    /**
     * @event SmsEvent an event raised right before send.
     * You may set [[SmsEvent::isValid]] to be false to cancel the send.
     */
    const EVENT_BEFORE_SEND = 'beforeSend';
    /**
     * @event SmsEvent an event raised right after send.
     */
    const EVENT_AFTER_SEND = 'afterSend';

    /**
     * @var array the configuration that should be applied to any newly created
     * email message instance by [[createMessage()]] or [[compose()]]. Any valid property defined
     * by [[MessageInterface]] can be configured, such as `from`, `to`, `body`, etc.
     *
     * For example:
     *
     * ```php
     * [
     *     'from' => '+123456789',
     * ]
     * ```
     */
    public $messageConfig = [];
    /**
     * @var string the default class name of the new message instances created by [[createMessage()]]
     */
    public $messageClass = 'mikk150\sms\BaseMessage';
    /**
     * @var bool whether to save email messages as files under [[fileTransportPath]] instead of sending them
     * to the actual recipients. This is usually used during development for debugging purpose.
     * @see fileTransportPath
     */
    public $useFileTransport = false;
    /**
     * @var string the directory where the email messages are saved when [[useFileTransport]] is true.
     */
    public $fileTransportPath = '@runtime/sms';

    public function compose($template = null, array $params = [])
    {
        $message = $this->createMessage();
        if ($template === null) {
            return $message;
        }

        $placeholders = [];
        foreach ((array) $params as $name => $value) {
            $placeholders['{' . $name . '}'] = $value;
        }

        $message->setBody($placeholders === [] ? $template : strtr($template, $placeholders));

        return $message;
    }

    /**
     * Creates a new message instance.
     * The newly created instance will be initialized with the configuration specified by [[messageConfig]].
     * If the configuration does not specify a 'class', the [[messageClass]] will be used as the class
     * of the new message instance.
     * @return MessageInterface message instance.
     */
    protected function createMessage()
    {
        $config = $this->messageConfig;
        if (!array_key_exists('class', $config)) {
            $config['class'] = $this->messageClass;
        }
        $config['provider'] = $this;
        return Yii::createObject($config);
    }

    /**
     * Sends the given sms message.
     * This method will log a message about the sms being sent.
     * If [[useFileTransport]] is true, it will save the sms as a file under [[fileTransportPath]].
     * Otherwise, it will call [[sendMessage()]] to send the sms to its recipient(s).
     * Child classes should implement [[sendMessage()]] with the actual sms sending logic.
     * @param MessageInterface $message sms message instance to be sent
     * @return bool whether the message has been sent successfully
     */
    public function send($message)
    {
        if (!$this->beforeSend($message)) {
            return false;
        }

        $address = $message->getTo();
        if (is_array($address)) {
            $address = implode(', ', array_keys($address));
        }
        Yii::info('Sending sms "' . $message->getBody() . '" to "' . $address . '"', __METHOD__);

        if ($this->useFileTransport) {
            $isSuccessful = $this->saveMessage($message);
        } else {
            $isSuccessful = $this->sendMessage($message);
        }
        $this->afterSend($message, $isSuccessful);

        return $isSuccessful;
    }

    /**
     * Sends multiple messages at once.
     *
     * The default implementation simply calls [[send()]] multiple times.
     * Child classes may override this method to implement more efficient way of
     * sending multiple messages.
     *
     * @param array $messages list of sms messages, which should be sent.
     * @return int number of messages that are successfully sent.
     */
    public function sendMultiple(array $messages)
    {
        $successCount = 0;
        foreach ($messages as $message) {
            if ($this->send($message)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Sends the specified message.
     * This method should be implemented by child classes with the actual email sending logic.
     * @param MessageInterface $message the message to be sent
     * @return bool whether the message is sent successfully
     */
    abstract protected function sendMessage($message);

    /**
     * This method is invoked right before mail send.
     * You may override this method to do last-minute preparation for the message.
     * If you override this method, please make sure you call the parent implementation first.
     * @param MessageInterface $message
     * @return bool whether to continue sending an sms.
     */
    public function beforeSend($message)
    {
        $event = new SmsEvent(['message' => $message]);
        $this->trigger(self::EVENT_BEFORE_SEND, $event);

        return $event->isValid;
    }

    /**
     * This method is invoked right after mail was send.
     * You may override this method to do some postprocessing or logging based on mail send status.
     * If you override this method, please make sure you call the parent implementation first.
     * @param MessageInterface $message
     * @param bool $isSuccessful
     */
    public function afterSend($message, $isSuccessful)
    {
        $event = new SmsEvent(['message' => $message, 'isSuccessful' => $isSuccessful]);
        $this->trigger(self::EVENT_AFTER_SEND, $event);
    }
}
