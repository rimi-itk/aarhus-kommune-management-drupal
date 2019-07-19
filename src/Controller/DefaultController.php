<?php

namespace Drupal\aarhus_kommune_management\Controller;

/**
 * Default controller.
 */
class DefaultController {

  /**
   * Handler.
   */
  public function handle() {
    $getUrl = function ($path) {
      return url('aarhus-kommune-management/' . $path, ['absolute' => TRUE]);
    };

    return [
      'authenticate' => $getUrl('authenticate'),
      'users' => $getUrl('users'),
    ];
  }

}
