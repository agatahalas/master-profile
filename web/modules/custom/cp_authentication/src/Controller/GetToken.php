<?php

namespace Drupal\cp_authentication\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\cp_authentication\CkidConnectorService;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * An example controller.
 */
class GetToken extends ControllerBase {

  /**
   * The CKID Connector service.
   *
   * @var \Drupal\cp_authentication\CkidConnectorService
   */
  protected $ckidConnectorService;

  /**
   * Request service.
   *
   * @var mixed
   */
  protected $request;

  /**
   * GetToken constructor.
   */
  public function __construct(CkidConnectorService $ckid_connector_service) {
    $this->ckidConnectorService = $ckid_connector_service;
    $this->request = \Drupal::request();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cp_authentication.ckid_connector')
    );
  }

  /**
   * Returns a token.
   */
  public function content() {
    $build = [
      '#markup' => $this->t('Get token'),
    ];

    $code = $this->request->query->get('code');

    try {
      $this->ckidConnectorService->getToken($code);
      return $this->redirect('cp_authentication.user_info');
    }
    catch (RequestException $e) {
      $build['#markup'] = $this->t('Exception: ' . $e->getMessage());
    }

    return $build;
  }

}
