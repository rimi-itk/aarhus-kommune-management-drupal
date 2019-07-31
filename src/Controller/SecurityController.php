<?php

namespace Drupal\aarhus_kommune_management\Controller;

use Drupal\aarhus_kommune_management\Security\SecurityManager;

/**
 * Authentication controller.
 */
class SecurityController extends ControllerBase {
  /**
   * The authentication manager.
   *
   * @var \Drupal\aarhus_kommune_management\Security\SecutiryManager
   */
  private $securityManager;

  /**
   * Constructor.
   */
  public function __construct(SecurityManager $securityManager) {
    $this->securityManager = $securityManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create() {
    return new static(
      new SecurityManager()
    );
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

    return $this->securityManager->createToken($payload);
  }

}
