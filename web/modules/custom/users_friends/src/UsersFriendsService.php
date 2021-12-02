<?php

namespace Drupal\users_friends;

use Drupal\Core\Database\Connection;
use Drupal\Component\Datetime\TimeInterface;

/**
 * Class UsersFriendsService.
 *
 * @package Drupal\users_friends
 */
class UsersFriendsService implements UsersFriendsInterface {

  /**
   * The default database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $connection;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * @param \Drupal\Core\Database\Connection $connection
   *   The default database connection.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */

  /**
   * Current user id.
   */
  protected int $currentUser;

  public function __construct(Connection $connection, TimeInterface $time) {
    $this->connection = $connection;
    $this->time = $time;
    $this->currentUser = \Drupal::currentUser()->id();
  }

  /**
   * Add friendship request.
   *
   * {@inheritdoc}
   * @throws \Exception
   */
  public function addRequest(int $requester, int $recipient): bool {
    // check whether a record already exists.
    $recordsCount = $this->connection->select('users_friends', 'x')
      ->condition('x.requester_uid', $requester)
      ->condition('x.recipient_uid', $recipient)
      ->countQuery()
      ->execute()
      ->fetchField();

    if($recordsCount == 0) {
      // insert data.
      $result = $this->connection->insert('users_friends')
        ->fields([
          'request_date' => $this->time->getRequestTime(),
          'requester_uid' => $requester,
          'recipient_uid' => $recipient,
          'status' => 0,
        ])
        ->execute();
    } else {
      $result = FALSE;
    }

    return $result;
  }

  /**
   * Cancel friendship request.
   *
   * {@inheritdoc}
   */
  public function cancelRequest(int $requester, int $recipient): bool {
    return $this->connection->delete('users_friends')
      ->condition('requester_uid', $requester)
      ->condition('recipient_uid', $recipient)
      ->execute();
  }

  /**
   * Accept friendship request.
   *
   * {@inheritdoc}
   */
  public function acceptRequest(int $requester, int $recipient): bool {
    return $this->connection->update('users_friends')
      ->fields([
        'status' => 1,
      ])
      ->condition('requester_uid', $requester)
      ->condition('recipient_uid', $recipient)
      ->execute();
  }

  /**
   * Decline friendship request.
   *
   * {@inheritdoc}
   */
  public function declineRequest(int $requester, int $recipient): bool {
    return $this->connection->delete('users_friends')
      ->condition('requester_uid', $requester)
      ->condition('recipient_uid', $recipient)
      ->execute();
  }

  /**
   * Remove friend.
   *
   * {@inheritdoc}
   */
  public function removeFriend(int $uid_1, int $uid_2): bool {
    $query = $this->connection->delete('users_friends');
    $andGroup1 = $query->andConditionGroup()
      ->condition('requester_uid', $uid_1)
      ->condition('recipient_uid', $uid_2);
    $andGroup2 = $query->andConditionGroup()
      ->condition('requester_uid', $uid_2)
      ->condition('recipient_uid', $uid_1);
    $orGroup = $query->orConditionGroup()
      ->condition($andGroup1)
      ->condition($andGroup2);
    $query->condition($orGroup);

    return $query->execute();
  }

  /**
   * Friends status.
   *
   * {@inheritdoc}
   */
  public function getFriendsStatus(int $uid_1, int $uid_2): string {
    $query = \Drupal::database()->select('users_friends', 'n');
    $query->addField('n', 'status');
    $query->addField('n', 'requester_uid');
    $query->addField('n', 'recipient_uid');
    $andGroup1 = $query->andConditionGroup()
      ->condition('n.requester_uid', $uid_1)
      ->condition('n.recipient_uid', $uid_2);
    $andGroup2 = $query->andConditionGroup()
      ->condition('n.requester_uid', $uid_2)
      ->condition('n.recipient_uid', $uid_1);
    $orGroup = $query->orConditionGroup()
      ->condition($andGroup1)
      ->condition($andGroup2);
    $query->condition($orGroup);
    $query->range(0, 1);
    $data = $query->execute()->fetchAssoc();

    switch (true) {
      case ($data['status'] == 0 && $data['requester_uid'] == $this->currentUser):
        $result = 'requester';
        break;
      case ($data['status'] == 0 && $data['recipient_uid'] == $this->currentUser):
        $result = 'recipient';
        break;
      case ($data['status'] == 1):
        $result = 'friends';
        break;
      default:
        $result = 'none';
    }

    return $result;
  }

  /**
   * Friends uids.
   *
   * {@inheritdoc}
   */
  public function getFriendsUids(int $uid): array {
    // TODO: Implement getFriendsUids() method.
  }

  /**
   * Friends data.
   *
   * {@inheritdoc}
   */
  public function getFriends(int $uid): string {
    // TODO: Implement getFriends() method.
  }

}