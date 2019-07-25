<?php

namespace Drupal\aarhus_kommune_management\Security\Repositories;

use Drupal\aarhus_kommune_management\Security\Entities\ScopeEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * Scope repository.
 */
class ScopeRepository implements ScopeRepositoryInterface {

  /**
   * {@inheritdoc}
   */
  public function getScopeEntityByIdentifier($scopeIdentifier) {
    $scopes = [
      'data:write' => [
        'description' => 'data:write',
      ],
      'data:read' => [
        'description' => 'date:read',
      ],
    ];

    if (array_key_exists($scopeIdentifier, $scopes) === FALSE) {
      return;
    }

    $scope = new ScopeEntity();
    $scope->setIdentifier($scopeIdentifier);

    return $scope;
  }

  /**
   * {@inheritdoc}
   */
  public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = NULL
    ) {
    return $scopes;
  }

}
