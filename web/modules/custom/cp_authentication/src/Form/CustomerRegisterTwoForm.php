<?php

namespace Drupal\cp_authentication\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class CustomerRegisterTwoForm extends MultistepFormBase {

  /**
   * @inheritDoc
   */
  public function getFormId()  {
    return 'cp_register_two_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['info'] = [
      '#type' => 'item',
      '#markup' => $this->t('We have sent a 6-digit verification code to ') . $this->store->get('phone'),
    ];

    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Code'),
      '#default_value' => $this->store->get('code') ? $this->store->get('code') : '',
      '#required' => TRUE,
    ];

    $form['actions']['previous'] = [
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#weight' => 0,
      '#url' => Url::fromRoute('cp_authentication.register_one'),
    ];

    $form['resend'] = [
      '#type' => 'item',
      '#markup' => Link::fromTextAndUrl(t('Resend code'), Url::fromRoute('cp_authentication.send_code'))->toString()
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('code', $form_state->getValue('code'));

    $form_state->setRedirect('cp_authentication.register_three');
  }
}
