<?php

namespace Drupal\custom_composer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Exception;
use Forecast\Forecast;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'CustomForecastBlock' block.
 *
 * @Block(
 *  id = "custom_forecast_block",
 *  admin_label = @Translation("Custom forecast block"),
 * )
 */
class CustomForecastBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->loggerFactory = $container->get('logger.factory');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'longitude' => '',
      'latitude' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['longitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => $this->configuration['longitude'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['latitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => $this->configuration['latitude'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['longitude'] = $form_state->getValue('longitude');
    $this->configuration['latitude'] = $form_state->getValue('latitude');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $output = [];

    try {
      $forecast_factory = new Forecast('7411b0e6d5e0c99fbd7405fd6de00cd5');
      $forecast = $forecast_factory->get(
        $this->configuration['latitude'],
        $this->configuration['longitude'],
        NULL,
        [
          'units' => 'si',
          'exclude' => 'flags',
        ]
      );

      $output = [
        '#markup' => $this->t('“Forecast is @summary with temperature of @temp deg C', [
          '@summary' => $forecast->currently->summary,
          '@temp' => $forecast->currently->temperature,
        ]),
      ];
    }
    catch (Exception $e) {
      $this->loggerFactory->get('custom_composer')
        ->error('Could not retrieve forecast data');
    }

    return $output;
  }

}
