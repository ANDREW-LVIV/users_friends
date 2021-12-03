<?php

namespace Drupal\users_friends\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Users Friends Form.
 */
class UsersFriendsForm extends FormBase {

  /**
   * Requester User id.
   *
   * @var int
   */
  protected int $requesterUid;

  /**
   * Recipient User id.
   *
   * @var int
   */
  protected int $recipientUid;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected AccountProxyInterface $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->currentUser = $container->get('current_user');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'users_friends_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->requesterUid = $this->currentUser->id();
    $this->recipientUid = \Drupal::routeMatch()->getParameter('user')->id();

    $form['friends_uids'] = [
      '#type' => 'markup',
      '#markup' => implode(', ', \Drupal::service('users_friends.manager')
        ->getFriendsUids($this->requesterUid)),
    ];

    if ($this->requesterUid === $this->recipientUid) {
      return $form;
    }

    $friendsStatus = \Drupal::service('users_friends.manager')
      ->getFriendsStatus($this->requesterUid, $this->recipientUid);

    if ($friendsStatus == 'none') {
      // Add friendship request button.
      $form['add_request'] = [
        '#type' => 'submit',
        '#value' => $this->t('Add to friend'),
        '#prefix' => '<div id="add-request">',
        '#suffix' => '</div>',
        '#ajax' => [
          'callback' => '::callbackAddRequestTrigger',
          'event' => 'click',
          'progress' => [
            'type' => 'none',
          ],
        ],
      ];
    }

    if ($friendsStatus == 'requester') {
      // Cancel friendship request button.
      $form['cancel_request'] = [
        '#type' => 'submit',
        '#value' => $this->t('cancel friendship request'),
        '#prefix' => '<div id="cancel-request">',
        '#suffix' => '</div>',
        '#ajax' => [
          'callback' => '::callbackCancelRequestTrigger',
          'event' => 'click',
          'progress' => [
            'type' => 'none',
          ],
        ],
      ];
    }

    if ($friendsStatus == 'recipient') {
      // Accept friendship request button.
      $form['accept_request'] = [
        '#type' => 'submit',
        '#value' => $this->t('Accept friendship request'),
        '#prefix' => '<div id="accept-request">',
        '#suffix' => '</div>',
        '#ajax' => [
          'callback' => '::callbackAcceptRequestTrigger',
          'event' => 'click',
          'progress' => [
            'type' => 'none',
          ],
        ],
      ];

      // Decline friendship request button.
      $form['decline_request'] = [
        '#type' => 'submit',
        '#value' => $this->t('Decline friendship request'),
        '#prefix' => '<div id="decline-request">',
        '#suffix' => '</div>',
        '#ajax' => [
          'callback' => '::callbackDeclineRequestTrigger',
          'event' => 'click',
          'progress' => [
            'type' => 'none',
          ],
        ],
      ];
    }

    if ($friendsStatus == 'friends') {
      // Remove friend button.
      $form['remove_friend'] = [
        '#type' => 'link',
        '#title' => $this->t('Remove friend'),
        '#url' => Url::fromRoute('users_friends.users_friends_delete_form',
          ['uid_1' => $this->requesterUid, 'uid_2' => $this->recipientUid]),
        '#attributes' => [
          'id' => 'remove-friend',
          'class' => 'button button--primary js-form-submit form-submit',
        ],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Ajax callback to add friendship request.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The JSON response object.
   */
  protected function callbackAddRequestTrigger(array &$form, FormStateInterface $form_state): AjaxResponse {
    /** @var \Drupal\users_friends\UsersFriendsService $addRequestService */
    $addRequestService = \Drupal::service('users_friends.manager')
      ->addRequest($this->requesterUid, $this->recipientUid);

    return $this->templateAjaxTrigger((bool) $addRequestService, '#add-request', $this->t('request sent'));
  }

  /**
   * Ajax callback to cancel friendship request.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The JSON response object.
   */
  protected function callbackCancelRequestTrigger(array &$form, FormStateInterface $form_state): AjaxResponse {
    /** @var \Drupal\users_friends\UsersFriendsService $cancelRequestService */
    $cancelRequestService = \Drupal::service('users_friends.manager')
      ->cancelRequest($this->requesterUid, $this->recipientUid);

    return $this->templateAjaxTrigger((bool) $cancelRequestService, '#cancel-request', $this->t('request canceled'));
  }

  /**
   * Ajax callback to accept friendship request.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The JSON response object.
   */
  protected function callbackAcceptRequestTrigger(array &$form, FormStateInterface $form_state): AjaxResponse {
    /** @var \Drupal\users_friends\UsersFriendsService $cancelRequestService */
    $cancelRequestService = \Drupal::service('users_friends.manager')
      ->acceptRequest($this->requesterUid, $this->recipientUid);

    return $this->templateAjaxTrigger((bool) $cancelRequestService, '#accept-request', $this->t('request accepted'));
  }

  /**
   * Ajax callback to decline friendship request.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The JSON response object.
   */
  protected function callbackDeclineRequestTrigger(array &$form, FormStateInterface $form_state): AjaxResponse {
    /** @var \Drupal\users_friends\UsersFriendsService $cancelRequestService */
    $cancelRequestService = \Drupal::service('users_friends.manager')
      ->declineRequest($this->requesterUid, $this->recipientUid);

    return $this->templateAjaxTrigger((bool) $cancelRequestService, '#decline-request', $this->t('request declined'));
  }

  /**
   * Template for Ajax callbacks.
   *
   * @param bool $status
   *   Response status.
   * @param string $selector
   *   HTML selector id.
   * @param string $responseText
   *   Response success text.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The JSON response object.
   */
  protected function templateAjaxTrigger(bool $status, string $selector, string $responseText): AjaxResponse {
    $response = new AjaxResponse();
    if ($status) {
      $response->addCommand(new HtmlCommand($selector, $responseText));
    }
    else {
      $response->addCommand(new HtmlCommand($selector, $this->t('something went wrong')));
    }

    return $response;
  }

}
