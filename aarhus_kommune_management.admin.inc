<?php

/**
 * @file
 * Admin stuff.
 */

/**
 * Admin form.
 */
function aarhus_kommune_management_admin_form($form, &$form_state) {
  $form['aarhus_kommune_management'] = [
    '#tree' => TRUE,
  ];

  $client_id = _aarhus_kommune_management_get_setting(['authentication', 'client_id']);
  $client_secret = _aarhus_kommune_management_get_setting(['authentication', 'client_secret']);

  $form['aarhus_kommune_management']['authentication'] = [
    '#type' => 'fieldset',
    '#title' => t('Authentication'),

    'client_id' => [
      '#type' => 'textfield',
      '#title' => t('Client id'),
      '#default_value' => $client_id ?? drupal_random_key(),
      '#description' => t('Client id'),
      '#required' => TRUE,
    ],
    'client_secret' => [
      '#type' => 'textfield',
      '#title' => t('Client secret'),
      '#default_value' => $client_secret ?? drupal_random_key(),
      '#description' => t('Client secret'),
      '#required' => TRUE,
    ],
  ];

  if ($client_id && $client_secret) {
    $authenticate_url = url('aarhus-kommune-management/authenticate', ['absolute' => TRUE]);
    $form['aarhus_kommune_management']['authentication']['examples'] = [
      'url' => [
        '#type' => 'textarea',
        '#default_value' => $authenticate_url,
      ],
      'curl' => [
        '#type' => 'textarea',
        '#default_value' => implode(PHP_EOL, [
          'curl ' . $authenticate_url . ' --data @- <<\'JSON\'',
          json_encode([
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'client_credentials',
            // 'scope' => 'data:read',
            'scope' => 'data:write',
          ], JSON_PRETTY_PRINT),
          'JSON',
        ]),
      ],
    ];
  }

  $form['aarhus_kommune_management']['users'] = [
    '#type' => 'fieldset',
    '#title' => t('Users'),

    'test_get_users' => [
      'button' => [
        '#type' => 'button',
        '#value' => t('Get users'),
        '#attributes' => [
          'formaction' => '/aarhus-kommune-management/users',
          'formmethod' => 'get',
          'formtarget' => 'test-users',
        ],
      ],

      'target' => [
        '#markup' => '<iframe style="width: 100%; height: 10em; border: solid 1px #aaa" name="test-users"></iframe>',
      ],
    ],
  ];

  return system_settings_form($form);
}
