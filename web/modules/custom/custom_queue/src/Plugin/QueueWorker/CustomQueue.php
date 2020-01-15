<?php

namespace Drupal\custom_queue\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the custom_queue queueworker.
 *
 * @QueueWorker (
 *   id = "custom_queue",
 *   title = @Translation("Sends a welcome email to newely registered users."),
 *   cron = {"time" = 30}
 * )
 */
class CustomQueue extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  private $mailManager;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;

  /**
   * The entity type manager.
   *
   * @var \Drupal\webprofiler\Entity\EntityManagerWrapper
   */
  private $accountStorage;

  /**
   * CustomQueue constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Mail\MailManagerInterface $mailManager
   *   The mail manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $accountStorage
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   The logger factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MailManagerInterface $mailManager, EntityTypeManagerInterface $accountStorage, LoggerChannelFactoryInterface $loggerChannelFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->mailManager = $mailManager;
    $this->accountStorage = $accountStorage;
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.mail'),
      $container->get('entity_type.manager'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $storage = $this->accountStorage->getStorage('user');
    $uid = $storage->getQuery()
      ->condition('uid', $data->uid)
      ->execute();

    $account = $storage->load(array_pop($uid));

    $result = $this->mailManager->mail(
      'custom_queue',
      'welcome_mail',
      $account->getEmail(),
      $account->getPreferredLangcode(),
      [],
      NULL,
      TRUE
    );

    if ($result['result'] !== TRUE) {
      // Log the message.
      $this->loggerChannelFactory->get('custom_queue')
        ->error('Error sending message: %uid %email.', [
          '%uid' => $data->uid,
          '%email' => '<' . $account->getEmail() . '>',
        ]);
    }
    else {
      // Log the message.
      $this->loggerChannelFactory->get('custom_queue')
        ->info('Welcome message sent: %uid %email.', [
          '%uid' => $data->uid,
          '%email' => '<' . $account->getEmail() . '>',
        ]);
    }
  }

}
