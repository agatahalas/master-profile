<?php

namespace Drupal\cp_authentication\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomerLoginForm extends FormBase {
  /**
   * A Ckid connector service.
   *
   * @var \Drupal\cp_authentication\CkidConnectorService
   */
  protected $ckidConnector;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->ckidConnector = $container->get('cp_authentication.ckid_connector');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cp_login_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $authorize_link = $this->ckidConnector->getAuthorizeLink();

    if ($this->ckidConnector->loggedIn()) {
      $this->ckidConnector->redirectToUserPage();
    }
    else {
      $this->ckidConnector->redirectToAuthenticatePage();
    }


    $form['kid_login'] = [
      '#type' => 'item',
      '#markup' => '<a class="button uk-button uk-button-secondary" href="' . $authorize_link .'">' . $this->t('CKID Log in') . '</a>',
    ];

    $form['description'] = [
      '#type' => 'markup',
      '#markup' => $this->t('To continue use your Circle K ID to log in'),
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#required' => TRUE,
    ];

    $form['remember'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remember me'),
      '#default_value' => 0,
      '#required' => FALSE,
    ];

    $form['forgot'] = [
      '#type' => 'item',
      '#markup' => Link::fromTextAndUrl(t('Forgot password?'), Url::fromRoute('cp_authentication.password'))->toString(),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Log in'),
    ];

    $form['signup_info'] = [
      '#type' => 'item',
      '#markup' => $this->t('Don\'t have an account?') . '<br>' . Link::fromTextAndUrl(t('Create'), Url::fromRoute('cp_authentication.register_one'))->toString(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addMessage('ok');
  }
}
