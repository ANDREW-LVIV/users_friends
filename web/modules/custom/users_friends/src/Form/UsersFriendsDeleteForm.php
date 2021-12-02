<?php

namespace Drupal\users_friends\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Defines a confirmation form to confirm deletion of something by id.
 */
class UsersFriendsDeleteForm extends ConfirmFormBase {

  /**
   * ID of the item to delete.
   *
   * @var int
   */
  protected int $uid_1;

  /**
   * ID of the item to delete.
   *
   * @var int
   */
  protected int $uid_2;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $uid_1 = NULL, string $uid_2 = NULL) {
    $this->uid_1 = $uid_1;
    $this->uid_2 = $uid_2;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::service('users_friends.manager')
      ->removeFriend($this->uid_1, $this->uid_2);

    $path = Url::fromRoute('user.page')->toString();
    $response = new RedirectResponse($path);
    $response->send();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() : string {
    return "confirm_delete_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('user.page');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Do you want to delete %uid_1 & %uid_2?', ['%uid_1' => $this->uid_1, '%uid_2' => $this->uid_2]);
  }

}