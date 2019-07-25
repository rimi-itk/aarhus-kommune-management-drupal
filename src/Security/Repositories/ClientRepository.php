<?php

namespace Drupal\aarhus_kommune_management\Security\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Drupal\aarhus_kommune_management\Security\Entities\ClientEntity;

/**
 * Client repository.
 */
class ClientRepository implements ClientRepositoryInterface {

  /**
   * {@inheritdoc}
   */
  public function getClientEntity($clientIdentifier) {
    $client = new ClientEntity();

    $client->setIdentifier($clientIdentifier);

    return $client;
  }

  /**
   * {@inheritdoc}
   */
  public function validateClient($clientIdentifier, $clientSecret, $grantType) {
    $clients = $this->getClients();

    foreach ($clients as $client) {
      if ($clientIdentifier === $client['id'] && $clientSecret === $client['secret']) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Get clients.
   */
  private function getClients() {
    return [
      [
        'id' => _aarhus_kommune_management_get_setting(['authentication', 'client_id']),
        'secret' => _aarhus_kommune_management_get_setting(['authentication', 'client_secret']),
      ],
    ];
  }

}
