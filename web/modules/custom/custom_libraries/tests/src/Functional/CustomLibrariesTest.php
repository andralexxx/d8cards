<?php

namespace Drupal\Tests\custom_libraries\Functional;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests below requirements.
 *
 * ● Attach the CSS such that it is loaded for all Table elements
 * shown anywhere on the site.
 * ● Take any custom block that you have built over the previous
 * exercises. Modify the build() to attach the JS to be loaded
 * whenever the block is displayed.
 *
 * @group custom_libraries
 */
class CustomLibrariesTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'block',
    'block_content',
    'custom_libraries',
    'custom_blocks',
  ];

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
    $this->user = $this->drupalCreateUser([
      'administer blocks',
      'administer site configuration',
      'access content overview',
      'access administration pages',
    ]);
    $this->drupalLogin($this->user);
  }

  /**
   * Tests that the CSS is loaded for all Table elements.
   */
  public function testCss() {
    // Open administration page to check CSS file.
    $this->drupalGet(Url::fromRoute('block.admin_display'));
    $this->assertSession()->statusCodeEquals(200);

    // Acquire CSS file.
    $this->assertSession()->responseContains('css/custom-libraries-theme.css');
  }

  /**
   * Tests that the JS is loaded whenever the custom block is displayed.
   */
  public function testJs() {
    // Create a block.
    $company_name = 'Snap Inc.';
    $block = [];
    $block['info[0][value]'] = $company_name;
    $block['field_company_name[0][value]'] = $company_name;
    $block['field_symbol[0][value]'] = 'SNAP';
    $this->drupalPostForm('block/add/stock_exchange_rate_card', $block, t('Save'));

    // Check that the block has been created.
    $this->assertRaw(new FormattableMarkup('@block %name has been created.', [
      '@block' => 'Stock Exchange Rate Card',
      '%name' => $block['info[0][value]'],
    ]));

    // Place our block to sidebar region.
    $this->submitForm(['region' => 'sidebar_first'], t('Save block'));
    $this->assertText('The block configuration has been saved.');
    $this->assertText($company_name);

    // Open homepage to observe Javascript file.
    $this->drupalGet(Url::fromRoute('<front>'));
    $this->assertSession()->statusCodeEquals(200);

    // Check for existence of the file.
    $this->assertSession()->responseContains('js/custom-libraries.js');
  }

}
