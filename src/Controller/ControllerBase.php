<?php

namespace Drupal\aarhus_kommune_management\Controller;

/**
 * Controller base.
 */
abstract class ControllerBase {

  /**
   * Create an instance of the controller.
   */
  public static function create() {
    return new static();
  }

}
