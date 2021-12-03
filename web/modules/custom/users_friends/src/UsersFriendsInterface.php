<?php

namespace Drupal\users_friends;

/**
 * Defines UsersFriends interface.
 */
interface UsersFriendsInterface {

  /**
   * Add friendship request.
   *
   * @param int $requester
   *   Requester id.
   * @param int $recipient
   *   Recipient id.
   *
   * @return bool
   *   Status of add request.
   */
  public function addRequest(int $requester, int $recipient): bool;

  /**
   * Cancel friendship request.
   *
   * @param int $requester
   *   Requester id.
   * @param int $recipient
   *   Recipient id.
   *
   * @return bool
   *   Status of cancel request.
   */
  public function cancelRequest(int $requester, int $recipient): bool;

  /**
   * Accept friendship request.
   *
   * @param int $requester
   *   Requester id.
   * @param int $recipient
   *   Recipient id.
   *
   * @return bool
   *   Status of accept request.
   */
  public function acceptRequest(int $requester, int $recipient): bool;

  /**
   * Decline friendship request.
   *
   * @param int $requester
   *   Requester id.
   * @param int $recipient
   *   Recipient id.
   *
   * @return bool
   *   Status of decline request.
   */
  public function declineRequest(int $requester, int $recipient): bool;

  /**
   * Remove friend.
   *
   * @param int $uid_1
   *   User id.
   * @param int $uid_2
   *   User id.
   *
   * @return bool
   *   Status of decline request.
   */
  public function removeFriend(int $uid_1, int $uid_2): bool;

  /**
   * Friends status.
   *
   * @param int $uid_1
   *   User id.
   * @param int $uid_2
   *   User id.
   *
   * @return string
   *   Status of friendship.
   */
  public function getFriendsStatus(int $uid_1, int $uid_2): string;

  /**
   * Friends uids.
   *
   * @param int $uid
   *   User id.
   *
   * @return array
   *   Array of friends ids.
   */
  public function getFriendsUids(int $uid): array;

  /**
   * Friends data.
   *
   * @param int $uid
   *   User id.
   *
   * @return string
   *   Friends data.
   */
  public function getFriends(int $uid): string;

}
