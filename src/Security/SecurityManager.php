<?php

namespace Drupal\aarhus_kommune_management\Security;

use Drupal\aarhus_kommune_management\Security\Repositories\AccessTokenRepository;
use Drupal\aarhus_kommune_management\Security\Repositories\ClientRepository;
use Drupal\aarhus_kommune_management\Security\Repositories\ScopeRepository;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\ResourceServer;

/**
 * Authentication manager.
 */
class SecurityManager {

  /**
   * Create token.
   */
  public function createToken() {
    $clientRepository = new ClientRepository();
    $scopeRepository = new ScopeRepository();
    $accessTokenRepository = new AccessTokenRepository();

    $privateKey = _aarhus_kommune_management_get_setting(['authentication', 'private_key']);
    $encryptionKey = _aarhus_kommune_management_get_setting(['authentication', 'encryption_key']);

    $server = new AuthorizationServer(
      $clientRepository,
      $accessTokenRepository,
      $scopeRepository,
      $privateKey,
      $encryptionKey
    );

    $server->enableGrantType(
      new ClientCredentialsGrant(),
    // Access tokens will expire after 1 hour.
      new \DateInterval('PT1H')
    );

    $request = ServerRequest::fromGlobals();
    $response = new Response();

    return $server->respondToAccessTokenRequest($request, $response);
  }

  /**
   * Validate token.
   */
  public function validateToken() {
    $accessTokenRepository = new AccessTokenRepository();
    $publicKey = _aarhus_kommune_management_get_setting(['authentication', 'public_key']);

    $server = new ResourceServer(
      $accessTokenRepository,
      $publicKey
    );
    $request = ServerRequest::fromGlobals();

    return $server->validateAuthenticatedRequest($request);
  }

}
