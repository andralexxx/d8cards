<?php

namespace Drupal\custom_text_filter\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;


/**
 * Custom text filter plugin.
 *
 * @Filter(
 *  id = "auto_capitalize",
 *  title = @Translation("Auto Capitalize"),
 *  description = @Translation("Auto­capitalizes pre­configured words anywhere they occur"),
 *  type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 *  settings = {
 *    "words_list" = "drupal, wordpress, joomla"
 *  },
 * )
 */
class AutoCapitalizeFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['words_list'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Words to Capitalize'),
      '#default_value' => $this->settings['words_list'],
      '#description' => $this->t('Enter list of words in small case, which should be capitalized.</br>Separate multiple words with coma (,)</br></br>Example: drupal, wordpress, joomla'),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function process($text, $langcode) {
    $words = explode(', ', $this->settings['words_list']);
    $replaces = array_map('ucfirst', $words);
    $new_text = str_replace($words, $replaces, $text);
    return new FilterProcessResult($new_text);
  }

}
