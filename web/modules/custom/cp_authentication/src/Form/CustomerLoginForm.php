<?php

namespace Drupal\cp_authentication\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class CustomerLoginForm extends FormBase {

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

    $form['login'] = [
      '#type' => 'item',
      //'#markup' => Link::fromTextAndUrl(t('Forgot password?'), Url::fromUri('https://test-circlekid-core-stable.test.gneis.io/api/v1/oauth/authorize?client_id=a418d653-a356-4d54-af20-28a9096d8c0f&response_type=code&scope=USER', []),
      '#markup' => '<a href="https://test-circlekid-core-stable.test.gneis.io/api/v1/oauth/authorize?client_id=a418d653-a356-4d54-af20-28a9096d8c0f&response_type=code&scope=USER&redirect_uri=https://master-profile.lndo.site/customer/get-token">' . $this->t('Log in') . '</a>',
    ];

    $form['description'] = [
      '#type' => 'markup',
      '#markup' => $this->t('To asdas continue use your Circle K ID to log in'),
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
