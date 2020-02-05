<?php

namespace Drupal\Tests\custom_content_entity_type\Kernel;

use Drupal\custom_content_entity_type\Entity\Contact;
use Drupal\KernelTests\KernelTestBase;

/**
 * Test basic CRUD operations for our Contact entity type.
 *
 * @group custom_content_entity_type
 * @group custom
 *
 * @ingroup custom_content_entity_type
 */
class ContactTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'custom_content_entity_type',
    'options',
    'user',
  ];

  /**
   * Basic CRUD operations on a Contact entity.
   */
  public function testEntity() {
    $this->installEntitySchema('contact');
    $entity = Contact::create([
      'type' => 'default',
      'name' => $this->randomString(),
      'email_address' => $this->randomString(),
      'telephone' => $this->randomString(),
      'address' => $this->randomString(),
      'user_id' => 0,
    ]);
    $this->assertNotNull($entity);
    $this->assertEquals(SAVED_NEW, $entity->save());
    $this->assertEquals(SAVED_UPDATED, $entity->set('user_id', 1)
      ->save());
    $entity_id = $entity->id();
    $this->assertNotEmpty($entity_id);
    $entity->delete();
    $this->assertNull(Contact::load($entity_id));
  }

}
