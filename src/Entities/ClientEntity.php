<?php

namespace Drupal\aarhus_kommune_management\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 *
 */
class ClientEntity implements ClientEntityInterface {
  use EntityTrait, ClientTrait;

  /**
   *
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   *
   */
  public function setRedirectUri($uri) {
    $this->redirectUri = $uri;
  }

}
