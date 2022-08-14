<?php

namespace Drupal\Tests\search_api\Functional;

use Drupal\block\Entity\Block;
use Drupal\Component\Utility\Html;
use Drupal\Core\Language\Language;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\entity_test\Entity\EntityTestMulRevChanged;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Utility\Utility;
use Drupal\views\Entity\View;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tests the option to get excerpts when there is an empty query string.
 *
 * @group search_api
 */
class EmptyQueryStringExcerptTest extends SearchApiBrowserTestBase {

  use ExampleContentTrait;

  /**
   * Modules to enable for this test.
   *
   * @var string[]
   */
  public static $modules = [
    'block',
    'language',
    'search_api_test_excerpt',
    'search_api_test_views',
    'views_ui',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->drupalLogin($this->adminUser);

    \Drupal::getContainer()
      ->get('search_api.index_task_manager')
      ->addItemsAll(Index::load($this->indexId));
    $this->insertExampleContent();
    $this->indexItems($this->indexId);

    // Do not use a batch for tracking the initial items after creating an
    // index when running the tests via the GUI. Otherwise, it seems Drupal's
    // Batch API gets confused and the test fails.
    if (!Utility::isRunningInCli()) {
      \Drupal::state()->set('search_api_use_tracking_batch', FALSE);
    }
  }

  /**
   * Tests the functionality with excerpt_always disabled.
   */
  public function testExcerptAlwaysDisabled() {
    // Set the 'excerpt_always' setting.
    $this->drupalGet('admin/config/search/search-api/index/' . $this->indexId . '/processors');
    $editForm = 'admin/config/search/search-api/index/' . $this->indexId . '/processors';
    $form = [
      'status[highlight]' => 1,
      'processors[highlight][settings][excerpt_always]' => 0,
    ];
    $this->drupalPostForm($editForm, $form, 'Save');

    $this->drupalGet('admin/config/search/search-api/index/' . $this->indexId);

    // Get the output of the view created in search_api_test_excerpt module.
    $this->drupalGet('search-api-test-search-excerpt');

    // The text is label field in the view that is hide if the field
    // doesn't exist. The Excerpt value without any query string is always
    // "..." and is a bad string to search.
    $this->assertSession()->pageTextNotContains('Excerpt_label');
  }

  /**
   * Tests the functionality with excerpt_always enabled.
   */
  public function testExcerptAlwaysEnabled() {

    // Set the 'excerpt_always' setting.
    $this->drupalGet('admin/config/search/search-api/index/' . $this->indexId . '/processors');
    $editForm = 'admin/config/search/search-api/index/' . $this->indexId . '/processors';
    $form = [
      'status[highlight]' => 1,
      'processors[highlight][settings][excerpt_always]' => 1,
    ];
    $this->drupalPostForm($editForm, $form, 'Save');

    $this->drupalGet('admin/config/search/search-api/index/' . $this->indexId);

    // Get the output of the view created in search_api_test_excerpt module.
    $this->drupalGet('search-api-test-search-excerpt');

    // The text is thle label field in the view that is hide if the field
    // doesn't exist. The Excerpt value without any query string is always
    // "..." and is a bad string to search.
    $this->assertSession()->pageTextContains('Excerpt_label');
  }

  /**
   * {@inheritdoc}
   */
  protected function initConfig(ContainerInterface $container) {
    parent::initConfig($container);

    // This will just set the Drupal state to include the necessary bundles for
    // our test entity type. Otherwise, fields from those bundles won't be found
    // and thus removed from the test index. (We can't do it in setUp(), before
    // calling the parent method, since the container isn't set up at that
    // point.)
    $bundles = [
      'entity_test_mulrev_changed' => ['label' => 'Entity Test Bundle'],
      'item' => ['label' => 'item'],
      'article' => ['label' => 'article'],    ];
    \Drupal::state()->set('entity_test_mulrev_changed.bundles', $bundles);
  }
}
