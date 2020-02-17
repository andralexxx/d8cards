<?php

namespace Drupal\custom_init\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent as FilterResponseEventAlias;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class CustomInitSubscriber.
 */
class CustomInitSubscriber implements EventSubscriberInterface {

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $account;

  /**
   * Constructs a new CustomInitSubscriber object.
   */
  public function __construct(AccountInterface $account) {
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['addAccessAllowOriginHeaders'];

    return $events;
  }

  /**
   * This method is called when the KernelEvents::RESPONSE is dispatched.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The dispatched event.
   */
  public function addAccessAllowOriginHeaders(FilterResponseEventAlias $event) {
    if ($this->account->id() === 0) {
      $response = $event->getResponse();
      $response->headers->set('Access-Control-Allow-Origin', '*');
    }
  }

}
