<?php

namespace Drupal\cp_authentication\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class CustomerRegisterThreeForm extends MultistepFormBase {

  /**
   * @inheritDoc
   */
  public function getFormId()  {
    return 'cp_register_three_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['info'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please enter contact info and create a password'),
    ];

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $this->store->get('name') ? $this->store->get('name') : '',
      '#required' => TRUE,
    );

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#default_value' => $this->store->get('email') ? $this->store->get('email') : '',
      '#required' => TRUE,
    ];

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#required' => TRUE,
    ];

    $form['actions']['previous'] = [
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#weight' => 0,
      '#url' => Url::fromRoute('cp_authentication.register_two'),
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('name', $form_state->getValue('name'));
    $this->store->set('email', $form_state->getValue('email'));
    $this->store->set('password', $form_state->getValue('password'));

    $form_state->setRedirect('cp_authentication.register_four');
  }
}
