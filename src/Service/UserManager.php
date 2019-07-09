<?php

namespace Drupal\aarhus_kommune_management\Service;

/**
 * User manager.
 */
class UserManager {

  /**
   * Get users.
   */
  public function getUsers() {
    return user_load_multiple([], ['status' => 1]);
  }

  /**
   * Create user.
   */
  public function createUser(array $data) {
    throw new \RuntimeException(__METHOD__ . ' not implemented');
  }

  /**
   * Update user.
   */
  public function updateUser(array $data) {
    throw new \RuntimeException(__METHOD__ . ' not implemented');
  }

  /**
   * Delete user.
   */
  public function deleteUser(array $data) {
    throw new \RuntimeException(__METHOD__ . ' not implemented');
  }

  /**
   * Serialize user.
   */
  public function serializeUser($user) {
    return [
      'uid' => $user->uid,
      'username' => $user->name,
      'email' => $user->mail,
    ];
  }

}
