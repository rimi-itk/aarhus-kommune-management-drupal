<?php

namespace Drupal\aarhus_kommune_management\Security\Entities;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

/**
 * Scope entity.
 */
class ScopeEntity implements ScopeEntityInterface {
  use EntityTrait, ScopeTrait;

}
