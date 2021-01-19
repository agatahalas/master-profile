<?php

/**
 * Prepares variables for cp_user_info_list template.
 *
 * Default template: cp-user-info-list.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - attributes: An associative array containing properties of the element.
 *   - items: An array containing elements with user data.
 */
function template_preprocess_cp_user_info_list(&$variables) {
  $variables['attributes']['class'][] = 'cp-user-info-list';

  $icon_client = \Drupal::service('cp_icons.icons_api');
  $icons = $icon_client->getIcons('service');

  foreach ($variables['items'] as $key => $item) {
    if (isset($icons[$item['icon']])) {
      $icon_url  = $icons[$item['icon']]['url'] . '?color=da291c';
      $icon_name = $icons[$item['icon']]['name'];
      $variables['items'][$key]['icon_markup'] = '<img class="uk-margin-right" src="' . $icon_url . '" alt="' . $icon_name . '" width="45"/>';
    }
  }
}
