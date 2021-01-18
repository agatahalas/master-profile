<?php

namespace Drupal\cp_icons;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Class IconsAPI.
 */
class IconsAPI {

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Icons API endpoint.
   *
   * @var string
   */
  protected $endpoint = 'https://slim.prod.cksites.net/api';

  /**
   * Constructs a new IconsAPI object.
   */
  public function __construct(ClientInterface $http_client, EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user) {
    $this->httpClient = $http_client;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * Get all default icons.
   */
  public function getIcons($category = NULL) {
    $icons_route = '/icons';
    $active_theme = \Drupal::config('system.theme')->get('default');
    if (isset($category)) {
      $icons_route .= '?category=' . $category;
    }
    $request = $this->httpClient->get($this->endpoint . $icons_route);
    $response = $request->getBody()->getContents();
    $contents = json_decode($response, TRUE);
    if (!empty($contents) && is_array($contents)) {
      return $contents;
    }
  }

}
