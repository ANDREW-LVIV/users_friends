<?php

namespace Drupal\users_friends\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class UsersFriendsController.
 */
class UsersFriendsController extends ControllerBase {

  /**
   * Page.
   *
   * @return string
   *   Return Hello string.
   */
  public function page() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: page')
    ];
  }

}
