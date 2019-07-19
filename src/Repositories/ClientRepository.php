<?php

namespace Drupal\aarhus_kommune_management\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Drupal\aarhus_kommune_management\Entities\ClientEntity;

/**
 *
 */
class ClientRepository implements ClientRepositoryInterface {
  const CLIENT_NAME = 'My Awesome App';
  const REDIRECT_URI = 'http://foo/bar';

  /**
   * {@inheritdoc}
   */
  public function getClientEntity($clientIdentifier) {
    $client = new ClientEntity();

    $client->setIdentifier($clientIdentifier);
    $client->setName(self::CLIENT_NAME);
    $client->setRedirectUri(self::REDIRECT_URI);

    return $client;
  }

  /**
   * {@inheritdoc}
   */
  public function validateClient($clientIdentifier, $clientSecret, $grantType) {
    $clients = [
      'myawesomeapp' => [
        'secret'          => password_hash('abc123', PASSWORD_BCRYPT),
        'name'            => self::CLIENT_NAME,
        'redirect_uri'    => self::REDIRECT_URI,
        'is_confidential' => TRUE,
      ],
    ];

    // Check if client is registered.
    if (array_key_exists($clientIdentifier, $clients) === FALSE) {
      return;
    }

    if (
          $clients[$clientIdentifier]['is_confidential'] === TRUE
          && password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === FALSE
      ) {
      return;
    }
  }

}
