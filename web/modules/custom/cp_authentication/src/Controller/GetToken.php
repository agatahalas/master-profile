<?php

namespace Drupal\cp_authentication\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\cp_authentication\CkidConnectorService;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * An example controller.
 */
class GetToken extends ControllerBase {

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
   * GetToken constructor.
   */
  public function __construct(CkidConnectorService $ckid_connector_service, RequestStack $request_stack) {
    $this->ckidConnectorService = $ckid_connector_service;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cp_authentication.ckid_connector'),
      $container->get('request_stack')
    );
  }

  /**
   * Returns a token.
   */
  public function content() {
    $build = [
      '#markup' => $this->t('Get token'),
    ];

    $code = $this->requestStack->getCurrentRequest()->query->get('code');

    try {
      $this->ckidConnectorService->getToken($code);
      return $this->redirect('cp_authentication.user_info');
    }
    catch (RequestException $e) {
      $build['#markup'] = $this->t('Exception: @message', ['@message' => $e->getMessage()]);
    }

    return $build;
  }

}
