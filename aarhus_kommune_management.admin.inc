<?php

/**
 * @file
 * Admin stuff.
 */

use Drupal\aarhus_kommune_management\Management\UserManager;

/**
 * Admin form.
 */
function aarhus_kommune_management_admin_form($form, &$form_state) {
  $form['aarhus_kommune_management'] = [
    '#tree' => TRUE,
  ];

  $client_id = _aarhus_kommune_management_get_setting(['authentication', 'client_id']);
  $client_secret = _aarhus_kommune_management_get_setting(['authentication', 'client_secret']);
  $site_dir = getcwd() . '/' . conf_path();

  $form['aarhus_kommune_management']['authentication'] = [
    '#type' => 'fieldset',
    '#title' => t('Authentication'),

    'private_key' => [
      '#type' => 'textfield',
      '#title' => t('Private key'),
      '#default_value' => _aarhus_kommune_management_get_setting(['authentication', 'private_key'], 'file://' . $site_dir . '/private.key'),
      '#description' => t('Private key file path'),
      '#required' => TRUE,
    ],

    'public_key' => [
      '#type' => 'textfield',
      '#title' => t('Public key'),
      '#default_value' => _aarhus_kommune_management_get_setting(['authentication', 'public_key'], 'file://' . $site_dir . '/public.key'),
      '#description' => t('Public key file path'),
      '#required' => TRUE,
    ],

    'encryption_key' => [
      '#type' => 'textfield',
      '#title' => t('Encryption key'),
      '#default_value' => _aarhus_kommune_management_get_setting(['authentication', 'encryption_key'], base64_encode(random_bytes(32))),
      '#description' => t('Encryption key'),
      '#required' => TRUE,
    ],

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
            // 'scope' => 'data:read',.
            'scope' => 'data:write',
          ], JSON_PRETTY_PRINT),
          'JSON',
        ]),
      ],

      'curl' => [
        '#type' => 'textarea',
        '#default_value' => implode(PHP_EOL, [
          'curl ' . $authenticate_url . ' \\',
          '--header "content-type: application/x-www-form-urlencoded" \\',
          // '--header "Accept: 1.0" \\',.
          '--data-urlencode "grant_type=client_credentials" \\',
          '--data-urlencode "client_id=' . $client_id . '" \\',
          '--data-urlencode "client_secret=' . $client_secret . '" \\',
          '--data-urlencode "scope=data:write"',
        ]),
      ]
    ];
  }

  $form['aarhus_kommune_management']['test'] = [
    '#type' => 'fieldset',
    '#title' => t('Test'),

    'test_get_users' => [
      'link' => [
        '#markup' => l(t('Get users'), 'admin/config/aarhus-kommune-management/test/users'),
      ],
    ],
  ];

  $form['aarhus_kommune_management']['advanced'] = [
    '#type' => 'fieldset',
    '#title' => t('Advanced'),

    'user_manager_class' => [
      '#type' => 'textfield',
      '#title' => t('User manager class'),
      '#default_value' => _aarhus_kommune_management_get_setting(['advanced', 'user_manager_class'], UserManager::class),
      '#description' => t('Class used for managing users. (default: %default).', ['%default' => UserManager::class]),
    ],
  ];

  $form['#validate'][] = 'aarhus_kommune_management_admin_form_validate';

  return system_settings_form($form);
}

/**
 * Validate form.
 */
function aarhus_kommune_management_admin_form_validate($form, &$form_state) {
  $authentication_settings = $form_state['values']['aarhus_kommune_management']['authentication'];
  $prefix = 'aarhus_kommune_management][authentication][';
  if (!is_readable($authentication_settings['private_key'])) {
    form_set_error($prefix . 'private_key', t('Private key file, %file, does not exist or cannot be read', ['%file' => $authentication_settings['private_key']]));
  }
  if (!is_readable($authentication_settings['public_key'])) {
    form_set_error($prefix . 'public_key', t('Public key file, %file, does not exist or cannot be read', ['%file' => $authentication_settings['public_key']]));
  }

  $advanced_settings = $form_state['values']['aarhus_kommune_management']['advanced'];
  $prefix = 'aarhus_kommune_management][advanced][';
  $class = $advanced_settings['user_manager_class'];
  if (!empty($class)) {
    if (!class_exists($class)) {
      form_set_error($prefix . 'user_manager_class',
        t('Class %class does not exist', ['%class' => $class]));
    }
    else {
      if (!is_a($class, UserManager::class, TRUE)) {
        form_set_error($prefix . 'user_manager_class',
          t('%class is not a subclass of %super_class',
            ['%class' => $class, '%super_class' => UserManager::class]));
      }
    }
  }
}
