<?php

namespace Drupal\Tests\custom_content_entity_type\Functional;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Tests\BrowserTestBase;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group custom_content_entity_type
 */
class CustomContentEntityTypeTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['custom_content_entity_type', 'field_ui', 'block'];

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
      'add contact entities',
      'edit contact entities',
      'view published contact entities',
      'view unpublished contact entities',
      'delete contact entities',
      'administer site configuration',
      'administer contact display',
      'administer contact entities',
    ]);
    $this->drupalLogin($this->user);

    $this->drupalPlaceBlock('system_menu_block:tools', ['region' => 'primary_menu']);
    $this->drupalPlaceBlock('local_tasks_block', ['region' => 'secondary_menu']);
    $this->drupalPlaceBlock('local_actions_block', ['region' => 'content']);
    $this->drupalPlaceBlock('page_title_block', ['region' => 'content']);
  }

  /**
   * Tests Day 11 task requirements.
   */
  public function testModuleFunctionality() {
    $assertSession = $this->assertSession();

    // Check that Content type bundle exists.
    $this->drupalGet('/admin/structure/contact_types');
    $assertSession->statusCodeEquals(200);
    $assertSession->pageTextContains('Contact type entities');
    $assertSession->pageTextContains('Default');

    // Check that required fields exists.
    $this->drupalGet('/admin/structure/contact_types/default/edit/display');
    $assertSession->statusCodeEquals(200);
    $assertSession->pageTextContains('Name');
    $assertSession->pageTextContains('Email Address');
    $assertSession->pageTextContains('Telephone');
    $assertSession->pageTextContains('Address');

    // Check that the content could be created.
    $this->drupalGet('/admin/content/contact');
    $assertSession->statusCodeEquals(200);

    $assertSession->linkExists('Add Contact');
    $this->clickLink('Add Contact');

    $assertSession->fieldValueEquals('Name', '');
    $assertSession->fieldValueEquals('Email Address', '');
    $assertSession->fieldValueEquals('Telephone', '');
    $assertSession->fieldValueEquals('Address', '');

    $user_ref = $this->user->name->value . ' (' . $this->user->id() . ')';
    $assertSession->fieldValueEquals('user_id[0][target_id]', $user_ref);

    $edit = [
      'Name' => 'test name',
      'Email Address' => $this->randomString(),
      'Telephone' => $this->randomString(),
      'Address' => $this->randomString(),
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');

    // Entity listed.
    $assertSession->linkExists('Edit');
    $assertSession->linkExists('Delete');
    $assertSession->responseContains(new FormattableMarkup('Created the %label Contact.', ['%label' => $edit['Name']]));

    // Entity shown.
    $assertSession->pageTextContains($edit['Name']);
    $assertSession->pageTextContains($edit['Email Address']);
    $assertSession->pageTextContains($edit['Telephone']);
    $assertSession->pageTextContains($edit['Address']);
    $assertSession->linkExists('View');
    $assertSession->linkExists('Edit');
    $assertSession->linkExists('Delete');

    // Delete the entity.
    $this->clickLink('Delete');

    // Confirm deletion.
    $assertSession->linkExists('Cancel');
    $this->drupalPostForm(NULL, [], 'Delete');

    // Back to list, must be empty.
    $assertSession->pageTextContains('There are no contact entities yet.');
  }

}
