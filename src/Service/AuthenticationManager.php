<?php

namespace Drupal\aarhus_kommune_management\Service;

/**
 * Authentication manager.
 */
class AuthenticationManager {

  /**
   * Create token.
   */
  public function createToken(array $data = NULL) {
    if (!isset(
    $data['client_id'],
    $data['client_secret'],
    $data['scope']
    )) {
      throw new \RuntimeException('Invalid authentication');
    }

    return [
      'access_token' => drupal_random_key(),
      'token_type' => 'Bearer',
      'expires_in' => 12345,
    ];
  }

  /**
   * Validate token.
   */
  public function validateToken($clientId, $clientSection, $scope) {
    return __METHOD__;
  }

}
