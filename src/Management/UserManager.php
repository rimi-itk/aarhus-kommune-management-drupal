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
  public function createUser($data) {
    if (!is_array($data) || !isset($data['uuid'])) {
      return 'invalid data';
    }
    $uuid = $data['uuid'];

    $user = self::loadUserByUuid($uuid);
    if (!empty($user)) {
      return [$uuid => 'User already exists'];
    }

    $hook = 'aarhus_kommune_management_user_create';
    $modules = module_implements($hook);
    if (!empty($modules)) {
      foreach ($modules as $module) {
        $user = module_invoke($module, $hook, $data);
        if (NULL !== $user) {
          $this->setUuid($user, $uuid);
          break;
        }
      }
    }

    if (NULL !== $user) {
      return [$uuid => 'User created'];
    }

    return [$uuid => empty($modules) ? 'Do not know how to create user' : 'Cannot create user'];
  }

  /**
   * Update user.
   */
  public function updateUser(array $data) {
    if (!isset($data['uuid'])) {
      return 'invalid data';
    }
    $uuid = $data['uuid'];

    $user = self::loadUserByUuid($uuid);
    if (empty($user)) {
      return [$uuid => 'No such user'];
    }

    $result = NULL;
    $hook = 'aarhus_kommune_management_user_update';
    $modules = module_implements($hook);
    if (!empty($modules)) {
      foreach ($modules as $module) {
        $result = module_invoke($module, $hook, $user, $data);
        if (!empty($result)) {
          break;
        }
      }
    }

    return [$uuid => !empty($result) ? 'User updated' : 'Do not know how to update user'];

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
    $uuid = $data['uuid'];

    $user = self::loadUserByUuid($uuid);
    if (empty($user)) {
      return [$uuid => 'No such user'];
    }

    $hook = 'aarhus_kommune_management_user_delete';
    $modules = module_implements($hook);
    if (!empty($modules)) {
      foreach ($modules as $module) {
        $result = module_invoke($module, $hook, $user, $data);
        if (!empty($result)) {
          return [$uuid => 'User deleted'];
        }
      }
    }

    return [$uuid => 'Do not know how to delete user'];

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
      $conditions = [
        ['user.status', 1],
      ];

      if (!empty($uids)) {
        $conditions[] = ['user.uid', $uids];
      }

      $users = $this->loadUsersByUuid([], $conditions);
    }

    return self::addUuids($users);
  }

  /**
   * Load users by uuids.
   *
   * @param array $uuids
   *   The uuids.
   * @param array $conditions
   *   Optionals conditions on `user` (the `user` table).
   *
   * @return object[]
   *   The users.
   */
  public static function loadUsersByUuid(array $uuids = NULL, array $conditions = []) {
    $query = db_select('aarhus_kommune_management_users', 'u')
      ->fields('u', ['uid', 'uuid']);

    if (!empty($conditions)) {
      $query->join('users', 'user', 'user.uid = u.uid');
      foreach ($conditions as $condition) {
        call_user_func_array([$query, 'condition'], $condition);
      }
    }

    if (!empty($uuids)) {
      $query->condition('u.uuid', $uuids);
    }

    $map = $query
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
    if (empty($users)) {
      return $users;
    }

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
   * Set uuid on user.
   *
   * @param object $user
   *   The user.
   * @param string $uuid
   *   The uuid.
   *
   * @throws \Exception
   */
  private function setUuid($user, $uuid) {
    return db_insert('aarhus_kommune_management_users')
      ->fields([
        'uid' => $user->uid,
        'uuid' => $uuid,
      ])
      ->execute();
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
