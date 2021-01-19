<?php

namespace Drupal\cp_user_basic_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Locale\CountryManager;
use Drupal\Core\Url;
use Drupal\cp_authentication\CkidConnectorService;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\cp_user_basic_data\CkidBasicData;

/**
 * An example controller.
 */
class BasicData extends ControllerBase {

  /**
   * The CKID Connector service.
   *
   * @var \Drupal\cp_authentication\CkidConnectorService
   */
  protected $ckidConnectorService;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * User basic data service.
   *
   * @var \Drupal\cp_user_basic_data\CkidBasicData
   */
   protected $ckidBasicData;

  /**
   * Construct.
   */
  public function __construct(CkidConnectorService $ckid_connector_service, RequestStack $request_stack, ClientInterface $http_client, CkidBasicData $ckid_basic_data) {
    $this->ckidConnectorService = $ckid_connector_service;
    $this->requestStack = $request_stack;
    $this->httpClient = $http_client;
    $this->ckidBasicData = $ckid_basic_data;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cp_authentication.ckid_connector'),
      $container->get('request_stack'),
      $container->get('http_client'),
      $container->get('cp_user_basic_data.ckid_basic_data')
    );
  }

  /**
   * Returns a user info.
   */
  public function content() {
    return $this->ckidBasicData->getData();
  }

}
