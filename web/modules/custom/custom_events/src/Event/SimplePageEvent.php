<?php

namespace Drupal\custom_events\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when simple page is viewed.
 *
 * @package Drupal\custom_events\Event
 */
class SimplePageEvent extends Event {
  const EVENT_NAME = 'custom_events_simple_page';
}
