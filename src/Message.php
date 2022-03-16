<?php

namespace Drupal\pushover;

use GuzzleHttp\Exception\RequestException;

class Message {
  protected $parameters = [];
  protected $sender;
  //protected $attachment;

  public function __construct(PushoverSender $sender, $message) {
    $this->parameters['message'] = $message;
    $this->parameters['priority'] = 0;
    $this->sender = $sender;
    return $this;
  }

  public function setTitle($title) {
    $this->parameters['title'] = $title;
    return $this;
  }

  public function setPriority($priority, $retry = 120, $expire = 86400) {
    $priority = (int)$priority;
    if ($priority >= -2 && $priority <= 2) {
      $this->parameters['priority'] = $priority;
      if ($priority == 2) {
        $this->parameters['retry'] = $retry;
        $this->parameters['expire'] = $expire;
      }
    }
    return $this;
  }

  public function setUrl($url, $url_title = NULL) {
    $this->parameters['url'] = $url;
    $this->parameters['url_title'] = $url_title;
    return $this;
  }

  public function setSound($sound) {
    $this->parameters['sound'] = $sound;
    return $this;
  }

  public function setTimestamp($timestamp) {
    $this->parameters['timestamp'] = (int)$timestamp;
    return $this;
  }

  public function setDevice($device) {
    $this->parameters['device'] = $device;
    return $this;
  }

  public function setUsers(Array $users) {
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

  public function send() {
    return $this->sender->send($this->parameters);
  }
}
