<?php

namespace Drupal\aarhus_kommune_management\Management;

/**
 * User manager.
 */
class UserManager {
  const UUID_FIELD_NAME = 'aarhus_kommune_management_uuid';
  const NOT_IMPLEMENTED = 'aarhus_kommune_management_not_implemented';

  /**
   * Create an instance of the manager.
   */
  public static function create() {
    return new static();
  }

  /**
   * Get users.
   */
  public function getUsers() {
    return $this->loadUsers();
  }

  /**
   * Create user.
   */
  public function createUser(array $data) {
    if (!isset($data['uuid'])) {
      return 'invalid data';
    }

    $hook = 'aarhus_kommune_management_user_create';
    if (!empty(module_implements($hook))) {
      return module_invoke_all($hook, $data);
    }

    return [$data['uuid'] => 'Do not know how to create user'];

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
    if (!isset($data['uuid'])) {
      return 'invalid data';
    }

    $user = self::loadUserByUuid($data['uuid']);

    if (FALSE === $user) {
      return [$data['uuid'] => 'No such user'];
    }

    $hook = 'aarhus_kommune_management_user_update';
    if (!empty(module_implements($hook))) {
      return module_invoke_all($hook, $user, $data);
    }

    return [$data['uuid'] => 'Do not know how to update user'];

    foreach (self::$properties as $key => $property) {
      if (isset($data[$key])) {
        $user->{$property} = $data[$key];
      }
    }

    $result = user_save($user);
    return [$data['uuid'] => FALSE === $result ? 'Error updating user' : 'User updated'];
  }

  /**
   * Delete user.
   */
  public function deleteUser(array $data) {
    if (!isset($data['uuid'])) {
      return 'invalid data';
    }

    $user = self::loadUserByUuid($data['uuid']);

    if (FALSE === $user) {
      return [$data['uuid'] => 'No such user'];
    }

    $hook = 'aarhus_kommune_management_user_delete';
    if (!empty(module_implements($hook))) {
      return module_invoke_all($hook, $user);
    }

    return [$data['uuid'] => 'Do not know how to delete user'];

    // user_delete($user);
  }

  /**
   * Serialize user.
   */
  public function serializeUser($user) {
    $result = $this->invokeOne('aarhus_kommune_management_user_serialize', $user);
    if (self::NOT_IMPLEMENTED !== $result) {
      return $result;
    }

    $data = [
      'uuid' => $user->aarhus_kommune_management_uuid ?: NULL,
      'email-address' => $user->mail,
    ];

    drupal_alter('aarhus_kommune_management_user_serialize', $data, $user);

    return $data;
  }

  /**
   * Load users by uids.
   *
   * @param array $uids
   *   The uids.
   */
  public function loadUsers(array $uids = []) {
    $hook = 'aarhus_kommune_management_user_list';
    $modules = module_implements($hook);
    if (!empty($modules)) {
      $users = module_invoke_all($hook, $uids);
    }
    else {
      $users = user_load_multiple($uids, ['status' => 1]);
    }

    return $this->addUuids($users);
  }

  /**
   * Load users by uuids.
   *
   * @param array $uuids
   *   The uuids.
   *
   * @return object[]
   *   The users.
   */
  public static function loadUsersByUuid(array $uuids) {
    $map = db_select('aarhus_kommune_management_users', 'u')
      ->condition('u.uuid', $uuids)
      ->fields('u', ['uid', 'uuid'])
      ->execute()
      ->fetchAllKeyed();

    $users = user_load_multiple(array_keys($map));

    return self::addUuids($users, $map);
  }

  /**
   * Load a user by uuid.
   *
   * @param string $uuid
   *   The uuid.
   *
   * @return bool|mixed
   *   The user if found.
   */
  public static function loadUserByUuid($uuid) {
    $users = self::loadUsersByUuid([$uuid]);

    return 1 === \count($users) ? reset($users) : FALSE;
  }

  /**
   * Add uuids to user objects.
   *
   * @param object[] $users
   *   The users.
   * @param array|null $map
   *   An optional map from user uid to user uuid.
   *
   * @return object[]
   *   The enriched users.
   */
  public static function addUuids(array $users, array $map = NULL) {
    $uids = array_map(function ($user) {
      return $user->uid;
    }, $users);

    if (NULL === $map) {
      $map = db_select('aarhus_kommune_management_users', 'u')
        ->condition('u.uid', $uids)
        ->fields('u', ['uid', 'uuid'])
        ->execute()
        ->fetchAllKeyed();
    }

    foreach ($users as $user) {
      $user->aarhus_kommune_management_uuid = $map[$user->uid] ?? NULL;
    }

    return $users;
  }

  /**
   * Invoke a single implementation (the one with highest priority) of a hook.
   *
   * @param string $hook
   *   The hook to invoke.
   *
   * @return mixed|string
   *   The result of invoking the hook if an implementation exists. Otherwise self::NOT_IMPLEMENTED.
   */
  private function invokeOne($hook) {
    $modules = module_implements($hook);
    if (empty($modules)) {
      return self::NOT_IMPLEMENTED;
    }

    $module = reset($modules);
    $args = func_get_args();
    array_unshift($args, $module);
    return call_user_func_array('module_invoke', $args);
  }

}
