<?php

namespace Drupal\aarhus_kommune_management\Service;

/**
 * User manager.
 */
class UserManager {
  /**
   * Key => user property.
   *
   * @var array
   */
  private static $properties = [
    'email' => 'mail',
    'username' => 'name',
  ];

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
    $user = (object) [];

    foreach (self::$properties as $key => $property) {
      if (isset($data[$key])) {
        $user->{$property} = $data[$key];
      }
    }

    if (!isset($user->name) && isset($user->mail)) {
      $user->name = $user->mail;
    }

    $result = user_save($user);

    return [FALSE === $result ? 'Error creating user' : 'User created'];
  }

  /**
   * Update user.
   */
  public function updateUser(array $data) {
    if (isset($data['uuid'])) {
      $uid = preg_replace('/^user:/', '', $data['uuid']);
      $user = user_load($uid);

      if (!$user) {
        return [$data['uuid'] => 'No such user'];
      }

      foreach (self::$properties as $key => $property) {
        if (isset($data[$key])) {
          $user->{$property} = $data[$key];
        }
      }

      $result = user_save($user);
      return [$data['uuid'] => FALSE === $result ? 'Error updating user' : 'User updated'];
    }

    return 'invalid data';
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
    $data = [
      'uuid' => 'user:' . $user->uid,
    ];

    foreach (self::$properties as $key => $property) {
      $data[$key] = $user->{$property};
    }

    return $data;
  }

}
