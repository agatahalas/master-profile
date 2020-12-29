<?php

namespace Drupal\cp_admin\Plugin\Block;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Routing\RouteProviderInterface;

/**
 * Provides a 'DashboardLink' block.
 *
 * @Block(
 *  id = "cp_admin_dashboard_link",
 *  admin_label = @Translation("Admin dashboard link"),
 * )
 */
class DashboardLink extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Route provider service.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteProviderInterface $route_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('router.route_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $link = [
      '#type' => 'link',
      '#title' => $this->t('Admin dashboard'),
      '#url' => Url::fromRoute('system.admin'),
    ];

    return $link;
  }

}
