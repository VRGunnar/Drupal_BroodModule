<?php

namespace Drupal\broodmodule\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *
 * @Block(
 *   id = "bread_module_block",
 *   admin_label = @Translation("Bread block"),
 *   category = @Translation("Hello bread"),
 * )
 */
class BroodModuleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\broodmodule\Form\SettingsForm');

    return [
      $form,
      '#attached' => ['library' => ['broodmodule/brood']],
    ];
  }

}
