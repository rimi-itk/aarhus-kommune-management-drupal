<?php

namespace Drupal\aarhus_kommune_management\Controller\Admin;

use Drupal\aarhus_kommune_management\Controller\ControllerBase;
use GuzzleHttp\Client;

/**
 * Admin test controller.
 */
class TestController extends ControllerBase {

  /**
   * Handle.
   */
  public function handle(array $path) {
    $action = array_shift($path);

    if ('users' === $action) {
      switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
          return $this->getUsers();
      }
    }

    throw new \Exception('Invalid request');
  }

  /**
   * Get users.
   */
  public function getUsers() {
    $result = [];

    try {
      $client = new Client();

      $url = url('/aarhus-kommune-management/authenticate', ['absolute' => TRUE]);
      $form_params = [
        'client_id' => _aarhus_kommune_management_get_setting(['authentication', 'client_id']),
        'client_secret' => _aarhus_kommune_management_get_setting(['authentication', 'client_secret']),
        'grant_type' => 'client_credentials',
        'scope' => 'data:write',
      ];

      $result['authentication']['request'] = [
        'url' => $url,
        'form_params' => $form_params,
      ];

      $response = $client->post($url, [
        'form_params' => $form_params,
      ]);

      $data = \json_decode((string) $response->getBody(), TRUE);

      $result['authentication']['response'] = $data;

      $url = url('/aarhus-kommune-management/users', ['absolute' => TRUE]);
      $token_type = $data['token_type'];
      $access_token = $data['access_token'];
      $headers = [
        'authorization' => $token_type . ' ' . $access_token,
      ];

      $result['users']['request'] = [
        'url' => $url,
        'headers' => $headers,
      ];

      $response = $client->get($url, [
        'headers' => $headers,
      ]);

      $data = \json_decode((string) $response->getBody(), TRUE);

      $result['users']['response'] = $data;

    }
    catch (\Exception $exception) {
      $result[] = $exception->getMessage();
    }

    return $result;
  }

}
