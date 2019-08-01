<?php

/**
 * @file
 * Hooks provided by aarhus_kommune_management.
 */

/**
 * List active (non-deleted) users.
 *
 * @param array|null $uids
 *   List only these users.
 *
 * @return object[]
 *   The user list.
 */
function hook_aarhus_kommune_management_user_list(array $uids = []) {

}

/**
 * Create user.
 *
 * @param array $data
 *   The user data.
 *
 * @return mixed
 *   @TODO: What to return?
 */
function hook_aarhus_kommune_management_user_create($data) {

}

/**
 * Update user.
 *
 * @param object $user
 *   The user.
 * @param array $data
 *   The user data.
 *
 * @return mixed
 *   @TODO: What to return?
 */
function hook_aarhus_kommune_management_user_update($user, array $data) {

}

/**
 * Delete user.
 *
 * @param object $user
 *   The user.
 *
 * @return mixed
 *   @TODO: What to return?
 */
function hook_aarhus_kommune_management_user_delete($user) {

}

/**
 * Serialize user.
 *
 * @param object $user
 *   The user.
 *
 * @return array
 *   The serialized user data.
 */
function hook_aarhus_kommune_management_user_serialize($user) {

}

/**
 * Serialize user (alter).
 *
 * @param array
 *   The serialized user data.
 * @param object $user
 *   The user.
 */
function hook_aarhus_kommune_management_user_serialize_alter(array &$data, $user) {

}
