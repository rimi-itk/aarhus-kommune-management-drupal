<?php

namespace Drupal\aarhus_kommune_management\Service;

use Drupal\aarhus_kommune_management\Repositories\AccessTokenRepository;
use Drupal\aarhus_kommune_management\Repositories\ClientRepository;
use Drupal\aarhus_kommune_management\Repositories\ScopeRepository;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\ResourceServer;

/**
 * Authentication manager.
 */
class AuthenticationManager {

  /**
   * Create token.
   */
  public function createToken() {
    $clientRepository = new ClientRepository();
    $scopeRepository = new ScopeRepository();
    $accessTokenRepository = new AccessTokenRepository();

    // Path to public and private keys.
    $privateKey = _aarhus_kommune_management_get_setting(['authentication', 'private_key']);
    // $privateKey = new CryptKey('file://path/to/private.key', 'passphrase'); // if private key has a pass phrase
    // Generate using base64_encode(random_bytes(32))
    $encryptionKey = 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen';

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
