<?php

namespace Drupal\Tests\custom_blocks\Functional;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\Traits\Core\CronRunTrait;

/**
 * Test to ensure that the module meets the requirements.
 *
 * @group custom_blocks
 */
class LoadTest extends BrowserTestBase {

  use CronRunTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['block', 'block_content', 'custom_blocks'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create user.
    $this->user = $this->drupalCreateUser([
      'administer blocks',
      'access administration pages',
    ]);
    $this->drupalLogin($this->user);
  }

  /**
   * Tests that the "Stock Exchange Rate Card" block type exists with 4 fields.
   */
  public function testBlockType() {
    // Step 1: Visit the add block administration page.
    $this->drupalGet('block/add');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Step 2: Check that the add block page opens.
    $assert->statusCodeEquals(200);

    // Step 3: Look on the page for the 'Stock Exchange Rate Card' link.
    $block_type_name = 'Stock Exchange Rate Card';
    $assert->linkExists('' . $block_type_name . '');

    // Step 4: Visit the add block administration page.
    $this->clickLink($block_type_name);

    // Step 5: Check that the page opens.
    $assert->statusCodeEquals(200);

    // Step 6: Look on the page for required fields.
    $assert->fieldExists('info[0][value]');
    $assert->fieldExists('field_company_name[0][value]');
    $assert->fieldExists('field_symbol[0][value]');
    $assert->fieldExists('field_last_price[0][value]');
    $assert->fieldExists('field_change[0][value]');

    // Step 6: Create Instance of the block
    // and place it at first sidebar region on the site.
    $blocks = [
      'SNAP' => 'Snap Inc.',
      'TWTR' => 'Twitter, Inc.',
      'VOD.L' => 'Vodafone Group plc',
    ];

    foreach ($blocks as $code => $company_name) {
      // Create a block.
      $block = [];
      $block['info[0][value]'] = $company_name;
      $block['field_company_name[0][value]'] = $company_name;
      $block['field_symbol[0][value]'] = $code;
      $this->drupalPostForm('block/add/stock_exchange_rate_card', $block, t('Save'));

      // Check that the block has been created.
      $this->assertRaw(new FormattableMarkup('@block %name has been created.', [
        '@block' => $block_type_name,
        '%name' => $block['info[0][value]'],
      ]));

      // Place our block to sidebar region.
      $this->submitForm(['region' => 'sidebar_first'], t('Save block'));
      $this->assertText('The block configuration has been saved.');
      $this->assertText($company_name);
    }

    $this->drupalGet('');
    $this->assertNoText('Last Price');
    $this->assertNoText('Change');

    // Check that there are no Price and Change fields before cron run.
    foreach ($blocks as $code => $company_name) {
      $xpathPrice = $this->xpath('//*[@id=:id]/div[contains(@class, :class)]', [
        ':id' => 'block-' . str_replace(['_', ' ', '.'], '', strtolower($company_name)),
        ':class' => 'field--name-' . str_replace('_', '-', 'field_last_price'),
      ]);
      $xpathChange = $this->xpath('//*[@id=:id]/div[contains(@class, :class)]', [
        ':id' => 'block-' . str_replace(['_', ' ', '.'], '', strtolower($company_name)),
        ':class' => 'field--name-' . str_replace('_', '-', 'field_change'),
      ]);
      $this->assertEqual(empty($xpathPrice), TRUE, 'Field Last price not found.');
      $this->assertEqual(empty($xpathChange), TRUE, 'Field Change not found.');
    }

    // Run cron to retrieve the data from API.
    $this->cronRun();
    $this->drupalGet('');

    // Check that we get the results after Cron run.
    foreach ($blocks as $code => $company_name) {
      $xpathPrice = $this->xpath('//*[@id=:id]/div[contains(@class, :class)]', [
        ':id' => 'block-' . str_replace(['_', ' ', '.', ','], '', strtolower($company_name)),
        ':class' => 'field--name-' . str_replace('_', '-', 'field_last_price'),
      ]);
      $xpathChange = $this->xpath('//*[@id=:id]/div[contains(@class, :class)]', [
        ':id' => 'block-' . str_replace(['_', ' ', '.', ','], '', strtolower($company_name)),
        ':class' => 'field--name-' . str_replace('_', '-', 'field_change'),
      ]);

      $this->assertEqual(count($xpathPrice), 1, 'Field Last price found for ' . $company_name);
      $this->assertEqual(count($xpathChange), 1, 'Field Change found for ' . $company_name);
    }
  }

}
