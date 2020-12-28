<?php

namespace Drupal\cp_authentication\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\RequestException;

/**
 * An example controller.
 */
class GetToken extends ControllerBase {

  /**
   * Returns a token.
   */
  public function content() {
    $build = [
      '#markup' => $this->t('Hello World!'),
    ];

    $request = \Drupal::request();
    $code = $request->query->get('code');

    $uri = 'https://test-circlekid-core-stable.test.gneis.io/api/v1/oauth/token';

    $username = 'a418d653-a356-4d54-af20-28a9096d8c0f';
    $pass = '166ba7d17ccfbea1c2120e0a7830d41f';
    $auth = 'Basic ' . base64_encode($username . ':' . $pass);

    try {
      $response = \Drupal::httpClient()->post($uri, [
        'headers' => [
          'Accept' => 'application/json',
          'Content-Type' => 'application/x-www-form-urlencoded',
          'Authorization' => $auth,
        ],
        'form_params' => [
          'code' => $code,
          'grant_type' => 'authorization_code',
          'scope' => 'USER',
          'redirect_uri' => 'https://master-profile.lndo.site/customer/get-token',
        ],
      ]);

      $body = json_decode($response->getBody()->getContents());

      if (!empty($body->access_token)) {
        user_cookie_save([
          'kid_token' => $body->access_token,
        ]);
        return $this->redirect('cp_authentication.user_info');
      }
    }
    catch (RequestException $e) {
      $build['#markup'] = $this->t('Exception: ' . $e->getMessage());
    }

    return $build;
  }

}
