<?php

namespace Drupal\cp_authentication\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class CustomerRegisterOneForm extends MultistepFormBase {

  /**
   * @inheritDoc
   */
  public function getFormId()  {
    return 'cp_register_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => [  // TODO: change it to method
        'dk' => 'Denmark',
        'se' => 'Sweden',
        'pl' => 'Poland',
      ],
      '#default_value' => $this->store->get('country') ? $this->store->get('country') : '',
      '#required' => TRUE,
    ];

    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number'),
     // '#pattern' => '[^\d]*',
      '#default_value' => $this->store->get('phone') ? $this->store->get('phone') : '',
      '#required' => TRUE,
    ];

    $form['actions']['submit']['#value'] = $this->t('Sign up');

    $form['login_info'] = [
      '#type' => 'item',
      '#markup' => $this->t('Already have an account?') . '<br>' . Link::fromTextAndUrl(t('Please log in'), Url::fromRoute('cp_authentication.login'))->toString(),
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('country', $form_state->getValue('country'));
    $this->store->set('phone', $form_state->getValue('phone'));
    $form_state->setRedirect('cp_authentication.register_two');
  }
}
