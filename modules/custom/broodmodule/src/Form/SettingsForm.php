<?php

namespace Drupal\broodmodule\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends FormBase
{

  public function getFormId()
  {
    return 'broodmodule_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name:'),
      '#required' => true,
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last name:'),
      '#required' => true,
    ];

    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number:'),
      '#required' => true,
    ];

    $form['bread'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Click here to order bread.'),
      '#required' => false,
    ];

    $form['pastry'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Click here to order pastry.'),
      '#required' => false,
    ];

    $form['bread_type'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Bread type:'),
      '#options' => [
        'wit_brood' => $this->t('White bread'),
        'bruin_brood' => $this->t('Brown bread'),
      ],
      '#states' => [
        'visible' => [
          ':input[name="bread"]' => [
            'checked' => TRUE,
          ]
        ]
      ]
    ];

    $form['pastry_type'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Pastry type:'),
      '#options' => [
        'chocolate_cream' => $this->t('Chocolate with cream'),
        'chocolate_bread' => $this->t("Chocolate bread")
      ],
      '#states' => [
        'visible' => [
          ':input[name="pastry"]' => [
            'checked' => TRUE,
          ]
        ]
      ]
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Order',
      '#button_type' => 'primary',
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    \Drupal::state()->set('broodmodule.first_name', $form_state->getValue('first_name'));
    \Drupal::state()->set('broodmodule.last_name', $form_state->getValue('last_name'));
    \Drupal::state()->set('broodmodule.phone_number', $form_state->getValue('phone_number'));
    \Drupal::state()->set('broodmodule.bread', $form_state->getValue('bread'));
    \Drupal::state()->set('broodmodule.pastry', $form_state->getValue('pastry'));
    \Drupal::state()->set('broodmodule.bread_type', $form_state->getValue('bread_type'));
    \Drupal::state()->set('broodmodule.pastry_type', $form_state->getValue('pastry_type'));

    $currentDay = date('w');
    if($currentDay === 2) {
      $date = date('l', strtotime("+ 2 day"));
    } else {
      $date = date('l', strtotime("+ 1 day"));
    }

    $orderId = \Drupal::database()->insert('orders')->fields([
      'first_name' => $form_state->getValue('first_name'),
      'last_name' => $form_state->getValue('last_name'),
      'phone_number' => $form_state->getValue('phone_number'),
      'bread' => $form_state->getValue('bread'),
      'pastry' => $form_state->getValue('pastry'),
      'bread_type' => implode(", ", array_filter($form_state->getValue('bread_type'))),
      'pastry_type' => implode(", ", array_filter($form_state->getValue('pastry_type'))),
    ])->execute();

    \Drupal::messenger()->addStatus('Bakkerij Peeters succesfully received your order! Your order number is ' . $orderId . '. You can pick up your order on ' . $date . ' between 07:00 and 17:00.');
  }
}
