<?php

namespace mikk150\sms;

/**
 *
 */
interface MessageInterface
{
    /**
     * Returns the message sender.
     * @return string the sender
     */
    public function getFrom();

    /**
     * Sets the message sender.
     * @param string|array $from sender email address.
     * You may pass an array of addresses if this message is from multiple people.
     * You may also specify sender name in addition to email address using format:
     * `[email => name]`.
     * @return $this self reference.
     */
    public function setFrom($from);

    public function getBody();

    public function setBody($body);

    /**
     * Returns the message recipient(s).
     * @return array the message recipients
     */
    public function getTo();

    /**
     * Sets the message recipient(s).
     * @param string|array $to receiver email address.
     * You may pass an array of addresses if multiple recipients should receive this message.
     * @return $this self reference.
     */
    public function setTo($to);

    /**
     * Sends this email message.
     * @param ProviderInterface $provider the provider that should be used to send this message.
     * If null, the "sms" application component will be used instead.
     * @return bool whether this message is sent successfully.
     */
    public function send(ProviderInterface $provider = null);

    /**
     * Returns string representation of this message.
     * @return string the string representation of this message.
     */
    public function toString();
}
