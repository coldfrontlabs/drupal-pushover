<?php

namespace Drupal\pushover;

use GuzzleHttp\Exception\RequestException;

/**
 *
 */
class PushoverSender {

  // Priority message constants.
  const EMERGENCY = 2;
  const HIGH = 1;
  const NORMAL = 0;
  const LOW = -1;
  const LOWEST = -2;

  protected $url = 'https://api.pushover.net/1/messages.json';
  protected $sound_url = 'https://api.pushover.net/1/sounds.json';
  public $options = [];

  protected $last_response = NULL;

  /**
   *
   */
  public function __construct() {
    $config = \Drupal::config('pushover.config')->getRawData();
    $this->options = [
      'token' => $config['api_key'],
      'user' => $config['user_key'],
      'sound' => $config['sound'],
    ];
    if (trim($config['devices']) !== '') {
      $this->options['device'] = $config['devices'];
    }
  }

  /**
   *
   */
  public function overrideUrl($url) {
    $this->url = $url;
  }

  /**
   *
   */
  public function message($message) {
    return new Message($this, $message);
  }

  /**
   *
   */
  public function send($message) {
    $client = \Drupal::httpClient();
    $options = array_merge($this->options, $message);
    try {
      $this->last_response = $client->request('POST', $this->url, ['form_params' => $options]);
      return $this->last_response->getStatusCode() == 200;
    }
    catch (RequestException $e) {
      watchdog_exception('pushover', $e);
      return FALSE;
    }
  }

  /**
   *
   */
  public function lastResponse() {
    return $this->last_response;
  }

  /**
   *
   */
  public function getSoundOptions() {
    $client = \Drupal::httpClient();
    try {
      $url = self::$sound_url . '?' . http_build_query(['token' => $this->options['data']['token']]);
      $response = $client->request('GET', $url);
      $sounds = json_decode((string) $response->getBody());
      return (array) $sounds->sounds;
    }
    catch (RequestException $e) {
      watchdog_exception('pushover', $e);
    }

    return FALSE;
  }

}
