<?php

namespace mikk150\sms;

/**
 *
 */
interface ProviderInterface
{
    /**
     * Creates a new message instance and optionally composes its body content.
     *
     * @param string|array|null $template the string to be used for rendering the message body. This can be:
     *
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return MessageInterface message instance.
     */
    public function compose($template = null, array $params = []);

    /**
     * Sends the given email message.
     * @param MessageInterface $message sms message instance to be sent
     * @return bool whether the message has been sent successfully
     */
    public function send($message);

    /**
     * Sends multiple messages at once.
     *
     * This method may be implemented by some providers which support more efficient way of sending multiple messages in the same batch.
     *
     * @param array $messages list of messages, which should be sent.
     * @return int number of messages that are successfully sent.
     */
    public function sendMultiple(array $messages);
}
