<?php

namespace Drupal\users_friends\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Url;


/**
 * Users Friends Form.
 */
class UsersFriendsForm extends FormBase {

  /**
   * @var integer
   */
  protected int $requesterUid;

  /**
   * @var integer
   */
  protected int $recipientUid;

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
    $this->requesterUid = \Drupal::currentUser()->id();
    $this->recipientUid = \Drupal::routeMatch()->getParameter('user')->id();

    $form['add_request'] = [
      '#type' => 'submit',
      '#value' => t('add to friend'),
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

    $form['cancel_request'] = [
      '#type' => 'submit',
      '#value' => t('cancel friendship request'),
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

    $form['accept_request'] = [
      '#type' => 'submit',
      '#value' => t('accept friendship request'),
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

    $form['decline_request'] = [
      '#type' => 'submit',
      '#value' => t('decline friendship request'),
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

    $form['remove_friend'] = [
      '#type' => 'link',
      '#title' => $this->t('remove friend'),
      '#url' => Url::fromRoute('users_friends.users_friends_delete_form', ['uid_1' => 1, 'uid_2' => 2]),
      '#attributes' => ['id'=>'remove-friend', 'class'=> 'button button--primary js-form-submit form-submit'],
    ];

//Додати у друзі (якщо ви знаходитесь на сторінці іншого користувача, з яким ви не друзі, та не відправляли йому заявку)
//Скасувати заявку у друзі (якщо ви знаходитесь на сторінці іншого користувача, якому ви вже відправили заявку)
//Прийняти заявку у друзі (якщо ви знаходитесь на сторінці іншого користувачаа, який відправив вам заявку)
//Відхилити заявку у друзі (якщо ви знаходитесь на сторінці іншого користувачаа, який відправив вам заявку)
//Видалити друга (якщо ви друзі із користувачем)

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
   * @param array $form
   * @param $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  function callbackAddRequestTrigger(array &$form, $form_state): AjaxResponse {
    /** @var \Drupal\users_friends\UsersFriendsService $addRequestService */
    $addRequestService = \Drupal::service('users_friends.manager')
      ->addRequest($this->requesterUid, $this->recipientUid);

    return $this->_templateAjaxTrigger((bool) $addRequestService, '#add-request', t('request sent'));
  }

  /**
   * @param array $form
   * @param $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  function callbackCancelRequestTrigger(array &$form, $form_state): AjaxResponse {
    /** @var \Drupal\users_friends\UsersFriendsService $cancelRequestService */
    $cancelRequestService = \Drupal::service('users_friends.manager')
      ->cancelRequest($this->requesterUid, $this->recipientUid);

    return $this->_templateAjaxTrigger((bool) $cancelRequestService, '#cancel-request', t('request canceled'));
  }

  /**
   * @param array $form
   * @param $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  function callbackAcceptRequestTrigger(array &$form, $form_state): AjaxResponse {
    /** @var \Drupal\users_friends\UsersFriendsService $cancelRequestService */
    $cancelRequestService = \Drupal::service('users_friends.manager')
      ->acceptRequest($this->requesterUid, $this->recipientUid);

    return $this->_templateAjaxTrigger((bool) $cancelRequestService, '#accept-request', t('request accepted'));
  }

  /**
   * @param array $form
   * @param $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  function callbackDeclineRequestTrigger(array &$form, $form_state): AjaxResponse {
    /** @var \Drupal\users_friends\UsersFriendsService $cancelRequestService */
    $cancelRequestService = \Drupal::service('users_friends.manager')
      ->declineRequest($this->requesterUid, $this->recipientUid);

    return $this->_templateAjaxTrigger((bool) $cancelRequestService, '#decline-request', t('request declined'));
  }

  /**
   * @param bool $status
   * @param string $selector
   * @param string $responseText
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  function _templateAjaxTrigger(bool $status, string $selector, string $responseText): AjaxResponse {
    $response = new AjaxResponse();
    if($status){
      $response->addCommand(new HtmlCommand($selector, $responseText));
    } else {
      $response->addCommand(new HtmlCommand($selector, t('something went wrong')));
    }

    return $response;
  }
}
