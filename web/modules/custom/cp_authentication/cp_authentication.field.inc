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
}
