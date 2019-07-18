<?php

namespace Drupal\aarhus_kommune_management\Controller;

use Drupal\aarhus_kommune_management\Service\UserManager;

/**
 * User controller.
 */
class UserController {
  /**
   * The user manager.
   *
   * @var \Drupal\aarhus_kommune_management\Service\UserManager
   */
  private $userManager;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->userManager = new UserManager();
  }

  /**
   * Handler.
   */
  public function handle() {
    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        return $this->get();

      case 'POST':
        return $this->update();
    }

    throw new \Exception('Invalid request');
  }

  /**
   * Get users.
   */
  public function get() {
    $data = $this->userManager->getUsers();

    $users = array_values(array_map([$this->userManager, 'serializeUser'], $data));

    return ['users' => $users];
  }

  /**
   * Update users.
   */
  public function update() {
    $payload = json_decode(file_get_contents('php://input'), TRUE);

    $result = [];

    if (isset($payload['create']) && is_array($payload['create'])) {
      foreach ($payload['create'] as $item) {
        $user = $this->userManager->createUser($item);
        $result['create'][] = $user;
      }
    }

    return $result;
  }

}
