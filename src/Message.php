<?php

namespace Drupal\pushover;

/**
 * Message class for building pushover messages.
 */
class Message {
  protected $parameters = [];
  protected $sender;

  /**
   * Build the message object.
   */
  public function __construct(PushoverSender $sender, $message) {
    $this->parameters['message'] = $message;
    $this->parameters['priority'] = 0;
    $this->sender = $sender;
    return $this;
  }

  /**
   * Set a title for the message.
   */
  public function setTitle($title) {
    $this->parameters['title'] = $title;
    return $this;
  }

  /**
   * Set the priority.
   */
  public function setPriority($priority, $retry = 120, $expire = 86400) {
    $priority = (int) $priority;
    if ($priority >= -2 && $priority <= 2) {
      $this->parameters['priority'] = $priority;
      if ($priority == 2) {
        $this->parameters['retry'] = $retry;
        $this->parameters['expire'] = $expire;
      }
    }
    return $this;
  }

  /**
   * Add a url to the message.
   */
  public function setUrl($url, $url_title = NULL) {
    $this->parameters['url'] = $url;
    $this->parameters['url_title'] = $url_title;
    return $this;
  }

  /**
   * Overide the sound to be played.
   */
  public function setSound($sound) {
    $this->parameters['sound'] = $sound;
    return $this;
  }

  /**
   * Override the message time.
   */
  public function setTimestamp($timestamp) {
    $this->parameters['timestamp'] = (int) $timestamp;
    return $this;
  }

  /**
   * Override devices.
   */
  public function setDevice($device) {
    $this->parameters['device'] = $device;
    return $this;
  }

  /**
   * Override recipients.
   *
   * @param array $users
   *   An array of user object to notify.
   */
  public function setUsers(array $users) {
    $user_keys = [];
    foreach ($users as $user) {
      if ($user->hasField('field_pushover_user_key') && $user->get('field_pushover_user_key')) {
        $user_keys[] = $user->get('field_pushover_user_key')->first()->getValue()['value'];
      }
    }
    if (!empty($user_keys)) {
      $this->parameters['user'] = implode(',', $user_keys);
    }
    return $this;
  }

  /**
   * Send the pushover message.
   */
  public function send() {
    return $this->sender->send($this->parameters);
  }

}
