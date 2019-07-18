<?php

namespace Drupal\aarhus_kommune_management\Controller;

use Drupal\aarhus_kommune_management\Service\AuthenticationManager;

/**
 * Authentication controller.
 */
class AuthenticationController {
  /**
   * The authentication manager.
   *
   * @var \Drupal\aarhus_kommune_management\Service\AuthenticationManager
   */
  private $authenticationManager;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->authenticationManager = new AuthenticationManager();
  }

  /**
   * Handler.
   */
  public function handle() {
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'POST':
        return $this->authenticate();
    }

    throw new \Exception('Invalid request');
  }

  /**
   * Authenticate.
   */
  public function authenticate() {
    $payload = json_decode(file_get_contents('php://input'), TRUE);

    return $this->authenticationManager->createToken($payload);
  }

}
