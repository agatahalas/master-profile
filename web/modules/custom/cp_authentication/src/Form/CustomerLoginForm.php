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
      '#markup' => $this->t('Don\'t have an account?') . '<br>' . Link::fromTextAndUrl(t('Create'), Url::fromRoute('cp_authentication.register'))->toString(),
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
