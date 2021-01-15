<?php

namespace Drupal\cp_links\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'User' condition.
 *
 * @Condition(
 *   id = "user",
 *   label = @Translation("Master profile user"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user", label = @Translation("User"))
 *   }
 * )
 */
class User extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->requestStack = $request_stack;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['roles'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('When the user is logged in to master profile account'),
      '#default_value' => $this->configuration['roles'],
      '#title' => $this->t('User is authenticated'),
      '#description' => $this->t('If the checkbox is not checked block is going to stay visible, however if the checkbox is checked then this block is going to displayed conditionally depending on whether master profile user is logged in or not.'),
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'roles' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['roles'] = $form_state->getValue('roles');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {

  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {

    if (!empty($this->configuration['roles']) && !$this->isNegated()) {
     /// $cookie_value = $this->requestStack->getCurrentRequest()->cookies->get('Drupal_visitor_kid_token');
      $tempstore = \Drupal::service('tempstore.private')->get('kid_session');
      $token = $tempstore->get('kid_access_token_value');
      if (!empty($token)) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // Optimize cache context, if a user cache context is provided, only use
    // user.roles, since that's the only part this condition cares about.
    $contexts = [];
    foreach (parent::getCacheContexts() as $context) {
      $contexts[] = $context == 'user' ? 'user.roles' : $context;
    }
    return $contexts;
  }

}
