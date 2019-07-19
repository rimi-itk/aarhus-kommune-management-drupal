<?php

namespace Drupal\aarhus_kommune_management\Entities;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 *
 */
class AccessTokenEntity implements AccessTokenEntityInterface {
  use AccessTokenTrait, TokenEntityTrait, EntityTrait;

}
