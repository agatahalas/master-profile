<?php

namespace Drupal\cp_authentication\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class CustomerRegisterFourForm extends MultistepFormBase {

  /**
   * @inheritDoc
   */
  public function getFormId()  {
    return 'cp_register_four_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['info'] = [
      '#type' => 'item',
      '#markup' => $this->t('Text text text text'),
    ];

    $form['tc'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Accept Terms & Conditions'),
      '#default_value' => $this->store->get('tc') ? $this->store->get('tc') : '',
      '#required' => TRUE,
    );

    $form['mc'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Accept Marketing consent'),
      '#default_value' => $this->store->get('mc') ? $this->store->get('mc') : '',
      '#required' => TRUE,
    ];

    $form['info_2'] = [
      '#type' => 'item',
      '#markup' => $this->t('You can always change your preferences in the settings'),
    ];

    $form['actions']['previous'] = [
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#weight' => 0,
      '#url' => Url::fromRoute('cp_authentication.register_three'),
    ];

    $form['actions']['submit']['#value'] = $this->t('Ok');

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('tc', $form_state->getValue('tc'));
    $this->store->set('mc', $form_state->getValue('mc'));

    // Save the data
    parent::saveData();
    $form_state->setRedirect('<front>');
  }
}
