<?php

namespace Drupal\aarhus_kommune_management\Repositories;

use Drupal\aarhus_kommune_management\Entities\AccessTokenEntity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

/**
 *
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface {

  /**
   * {@inheritdoc}
   */
  public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity) {
    // Some logic here to save the access token to a database.
  }

  /**
   * {@inheritdoc}
   */
  public function revokeAccessToken($tokenId) {
    // Some logic here to revoke the access token.
  }

  /**
   * {@inheritdoc}
   */
  public function isAccessTokenRevoked($tokenId) {
    // Access token hasn't been revoked.
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = NULL) {
    $accessToken = new AccessTokenEntity();
    $accessToken->setClient($clientEntity);
    foreach ($scopes as $scope) {
      $accessToken->addScope($scope);
    }
    $accessToken->setUserIdentifier($userIdentifier);

    return $accessToken;
  }

}
