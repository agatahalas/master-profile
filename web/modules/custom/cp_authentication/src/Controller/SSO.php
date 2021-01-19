<?php

namespace Drupal\cp_authentication\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\SessionManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\cp_authentication\CkidConnectorService;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Logger\LoggerChannelFactory;

/**
 * Single sign on.
 */
class SSO extends ControllerBase {

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
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The CKID Connector service.
   *
   * @var \Drupal\cp_authentication\CkidConnectorService
   */
  protected $ckidConnectorService;

  /**
   * Logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(PrivateTempStoreFactory $temp_store, SessionManager $session_manager, RequestStack $request_stack, CkidConnectorService $ckid_connector_service, LoggerChannelFactory $logger) {
    $this->tempStore = $temp_store;
    $this->sessionManager = $session_manager;
    $this->requestStack = $request_stack;
    $this->ckidConnectorService = $ckid_connector_service;
    $this->loggerFactory = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $temp_store = $container->get('tempstore.private');
    $session_manager = $container->get('session_manager');
    $request_stack = $container->get('request_stack');
    $ckid_connector_service = $container->get('cp_authentication.ckid_connector');
    $logger = $container->get('logger.factory');
    return new static($temp_store, $session_manager, $request_stack, $ckid_connector_service, $logger);
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

  /**
   * Redirect user to dashboard.
   */
  public function loginRedirect() {
    $code = $this->requestStack->getCurrentRequest()->query->get('code');

    try {
      $this->ckidConnectorService->getToken($code);
      return $this->redirect('cp_authentication.user_info');
    }
    catch (RequestException $e) {
      $this->loggerFactory->get('SSO')->warning('Initial login token could not be generated. Exception: @message', ['@message' => $e->getMessage()]);
      return new JsonResponse([]);
    }
  }

}
