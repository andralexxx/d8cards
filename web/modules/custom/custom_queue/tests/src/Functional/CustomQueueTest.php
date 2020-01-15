<?php

namespace Drupal\Tests\custom_queue\Functional;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\Traits\Core\CronRunTrait;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group custom_queue
 */
class CustomQueueTest extends BrowserTestBase {

  use CronRunTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['custom_queue'];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * A test user with authenticated permission.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * Queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  private $queueFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->adminUser = $this->createUser([
      'administer site configuration',
      'access site reports',
    ]);
    $this->drupalLogin($this->adminUser);
    $this->queueFactory = \Drupal::service('queue');
  }

  /**
   * Tests that the home page loads with a 200 response.
   */
  public function testCustomQueue() {
    // 1. Check that the module created and the settings page accessible.
    $this->drupalGet(Url::fromRoute('custom_queue.custom_queue_config_form'));
    $assert = $this->assertSession();
    $assert->statusCodeEquals(200);
    $assert->fieldValueEquals('Subject', 'Thank you for registering');
    $assert->fieldValueEquals('Message text', "Hooray! You've just registered on our site.");

    // 2. Check that the Queue doesn't contain the UIDs.
    $queue = $this->queueFactory->get('custom_queue');
    $numberOfItems = $queue->numberOfItems();
    $assert->assert($numberOfItems === 1, 'There is one item in queue');

    // 3. Register user and check that the Queue contains the UID.
    $this->user = $this->createUser();
    $assert->assert(is_numeric($this->user->id()), 'The user created');
    $assert->assert($queue->numberOfItems() === ++$numberOfItems, 'Tne number in queue increased by one');

    // 4. Run Cron.
    $this->cronRun();

    // 5. Check that queue is empty.
    $assert->assert($queue->numberOfItems() === 0, 'Tne number in queue increased by one');

    // 6. View the database log report.
    $this->drupalGet('admin/reports/dblog');
    $this->assertResponse(200);

    // 7. Check that there is log message.
    $this->assertLogMessage(t('Welcome message sent: %uid %email.', [
      '%uid' => $this->user->id(),
      '%email' => '<' . $this->user->getEmail() . '>',
    ]));
  }

  /**
   * Confirms that a log message appears on the database log overview screen.
   *
   * This function should only be used for the admin/reports/dblog page, because
   * it checks for the message link text truncated to 56 characters. Other log
   * pages have no detail links so they contain the full message text.
   *
   * @param string $log_message
   *   The database log message to check.
   */
  protected function assertLogMessage($log_message) {
    $message_text = Unicode::truncate(Html::decodeEntities(strip_tags($log_message)), 56, TRUE, TRUE);
    $this->assertLink($message_text, 0);
  }

}
