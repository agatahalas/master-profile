<?php

namespace Drupal\cp_links\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides social icon block with configurations.
 *
 * @Block(
 *   id = "cp_links",
 *   admin_label = @Translation("Links"),
 * )
 */
class LinksBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $url = Url::fromUri('http://test.me/go/here');
    $build = [
      '#theme' => 'item_list',
      '#items' => [
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Profile'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Account overview'),
            ],
          ],
        ],
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Services'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Carwash subscription'),
            ],
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Click and collect'),
            ],
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Fuel'),
            ],
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Electric vehicle'),
            ],
          ],
        ],
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Payment'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Payment methods'),
            ],
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Transactions'),
            ],
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Invoice date'),
            ],
          ],
        ],
        [
          '#theme' => 'item_list',
          '#title' => $this->t('Extra club'),
          '#items' => [
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('Partner agreements'),
            ],
            [
              '#type' => 'link',
              '#url' => $url,
              '#title' => $this->t('About ExtraClub'),
            ],
          ],
        ],
      ],
    ];

    return $build;
  }

}
