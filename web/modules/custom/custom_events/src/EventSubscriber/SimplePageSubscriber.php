<?php

namespace Drupal\custom_events\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Class SimplePageSubscriber.
 */
class SimplePageSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructs a new SimplePageSubscriber object.
   */
  public function __construct(LoggerChannelFactoryInterface $logger_factory) {
    $this->loggerFactory = $logger_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['custom_events_simple_page'] = ['onSimplePageView'];

    return $events;
  }

  /**
   * This method is called when the custom_events_simple_page is dispatched.
   *
   * @param \Symfony\Component\EventDispatcher\Event $event
   *   The dispatched event.
   */
  public function onSimplePageView(Event $event) {
    $this->loggerFactory->get('custom_events')->debug('Event custom_events_simple_page thrown by Subscriber in module custom_events.');
    \Drupal::messenger()->addMessage('Event custom_events_simple_page thrown by Subscriber in module custom_events.', 'status', TRUE);
  }

}
