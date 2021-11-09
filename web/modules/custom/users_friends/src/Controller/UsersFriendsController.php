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
      '#markup' => $this->t('main page')
    ];
  }

  /**
   * Friends List Page.
   *
   * @param int $uid
   *   User ID.
   *
   * @return string
   *   Return string.
   */
  public function friendsList(int $uid) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Friends List')
    ];
  }

  /**
   * Friends Requests Page.
   *
   * @param int $uid
   *   User ID.
   *
   * @return string
   *   Return string.
   */
  public function friendsRequests(int $uid) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Friends Requests')
    ];
  }
}
