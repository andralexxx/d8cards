<?php

namespace Drupal\Tests\custom_text_filter\Functional;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\BrowserTestBase;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group custom_text_filter
 */
class CustomTextFilterTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['filter', 'node', 'custom_text_filter'];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected $profile = 'testing';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * List of words to check.
   *
   * @var array
   */
  private $words = ['drupal', 'wordpress', 'joomla'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser([
      'administer site configuration',
      'administer filters',
      'administer content types',
    ]);

    $this->drupalCreateContentType([
      'type' => 'page',
      'name' => 'Basic page',
      'display_submitted' => FALSE,
    ]);

    $this->drupalLogin($this->user);
  }

  /**
   * Tests task requirements.
   */
  public function testCustomTextFilter() {
    /*
     * * Create a custom text filter that could be added to any text
     *   format, which auto­capitalizes pre­configured words anywhere
     *   they occur in the filtered text.
     */

    $assertSession = $this->assertSession();
    $page = $this->getSession()->getPage();
    $wordsToCapitalize = 'drupal, wordpress, joomla';
    $formats = FilterFormat::loadMultiple();
    $assertSession->assert(!empty($formats), 'There is at least one text format available.');

    // Check that the text filter could be added to any text format.
    foreach ($formats as $index => $format) {
      // Open the text format config page.
      $this->drupalGet('/admin/config/content/formats/manage/' . $format->id());
      $assertSession->statusCodeEquals(200);

      // Check that the filter format available.
      $assertSession->pageTextContains('Auto Capitalize');
      $filterFieldLocator = 'filters[auto_capitalize][status]';
      $page->checkField($filterFieldLocator);

      // Check that the filter has configuration form.
      $configurationForm = $page->find('named', [
        'id',
        'edit-filters-auto-capitalize-settings',
      ]);
      $assertSession->assert(!empty($configurationForm), 'Configuration form found');
      $assertSession->fieldValueEquals('filters[auto_capitalize][settings][words_list]', $wordsToCapitalize, $configurationForm);
      $assertSession->pageTextContains('Words to Capitalize');
      $assertSession->responseContains('Enter list of words in small case, which should be capitalized.</br>Separate multiple words with coma (,)</br></br>Example: drupal, wordpress, joomla');

      // Enable the format.
      $page->pressButton('Save configuration');

      // Check that form saved.
      $assertSession->statusCodeEquals(200);
      $assertSession->responseContains((string) new FormattableMarkup('The text format %format has been updated.', [
        '%format' => $format->label(),
      ]));

      // Check that settings applied.
      $assertSession->assert($page->findField($filterFieldLocator)
        ->isChecked(), 'Filter enabled');
    }

    // Add test content with mixed words to capitalize.
    $inputString = 'test drupal symfony wordpress joomla';
    $expectedString = 'test Drupal symfony Wordpress Joomla';

    // Create a node.
    $this->drupalGet('/node/add');
    $assertSession->statusCodeEquals(200);

    $edit = [];
    $edit['title[0][value]'] = 'Test node';
    $edit['body[0][value]'] = $inputString;
    $this->drupalPostForm('node/add/page', $edit, t('Save'));

    // Check that the Basic page has been created.
    $assertSession->pageTextContains((string) new FormattableMarkup('@type @title has been created', [
      '@type' => 'Basic page',
      '@title' => $edit['title[0][value]'],
    ]));

    // Check that only configured words capitalized.
    $node = $this->drupalGetNodeByTitle($edit['title[0][value]']);
    $this->assertNotEmpty($node, 'Node found in database.');

    $body = $page->find('css', 'article')->getText();
    $assertSession->assert(strpos($body, $expectedString) !== FALSE, 'The words in Body capitalized.');
  }

}
