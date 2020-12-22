<?php

namespace Drupal\cp_authentication\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An example controller.
 */
class UserInfo extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function content() {
    $build = [
      '#markup' => $this->t('Hello from user info!'),
    ];

    // Cookie value.
    $value = \Drupal::request()->cookies->get('Drupal_visitor_kid_token');

    if ($value) {
      try {
        $uri = 'https://test-circlekid-core-stable.test.gneis.io/api/v2/oauth/userinfo';
        $response = \Drupal::httpClient()->get($uri, [
          'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $value,
          ],
        ]);

        $body = json_decode($response->getBody()->getContents());
        dd($body);
      }
      catch (RequestException $e) {
        return FALSE;
      }
    }
    return $build;
  }

}
