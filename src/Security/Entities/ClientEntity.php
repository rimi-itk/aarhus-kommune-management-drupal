<?php

namespace Drupal\aarhus_kommune_management\Security\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 * Client entity.
 */
class ClientEntity implements ClientEntityInterface {
  use EntityTrait, ClientTrait;

  /**
   * Set name.
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Set redirect url.
   */
  public function setRedirectUri($uri) {
    $this->redirectUri = $uri;
  }

}
