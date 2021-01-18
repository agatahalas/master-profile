<?php

namespace Drupal\cp_authentication\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\SessionManager;

/**
 * Single sign on logout.
 */
class SSOLogout extends ControllerBase {

  /**
   * The temp store.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $tempStore;

  /**
   * Session manager.
   *
   * @var \Drupal\Core\Session\SessionManager
   */
  protected $sessionManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(PrivateTempStoreFactory $temp_store, SessionManager $session_manager) {
    $this->tempStore = $temp_store;
    $this->sessionManager = $session_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $temp_store = $container->get('tempstore.private');
    $session_manager = $container->get('session_manager');
    return new static($temp_store, $session_manager);
  }

  /**
   * Logout user by ckid request call.
   */
  public function logout(Request $request) {
    $tempstore = $this->tempStore->get('kid_session');
    $tempstore->delete('kid_access_token_value');
    $tempstore->delete('kid_refresh_token_value');
    $tempstore->delete('kid_token_expire');
    $tempstore->delete('kid_client_id');
    $this->sessionManager->destroy();

    return new JsonResponse([
      'ok',
    ]);
  }

}
