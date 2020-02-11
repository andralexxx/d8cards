<?php

namespace Drupal\custom_field_formatter\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'stars_rating_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "stars_rating_formatter",
 *   label = @Translation("Stars rating formatter"),
 *   field_types = {
 *     "decimal"
 *   }
 * )
 */
class StarsRatingFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#rating' => $this->viewValue($item),
        '#theme' => 'custom_field_formatter',
        '#attached' => [
          'library' => ['custom_field_formatter/custom_rating'],
        ],
      ];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $value = Html::escape($item->value) * 2;
    $value = round($value, 0, PHP_ROUND_HALF_UP) / 0.2;
    return intval($value);
  }

}
