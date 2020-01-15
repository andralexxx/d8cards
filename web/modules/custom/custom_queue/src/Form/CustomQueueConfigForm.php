<?php

namespace Drupal\custom_queue\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CustomQueueConfigForm.
 */
class CustomQueueConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_queue_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::configFactory()->getEditable('custom_queue.settings');

    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('subject'),
    ];
    $form['message_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message text'),
      '#default_value' => $config->get('message_text'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    \Drupal::configFactory()->getEditable('custom_queue.settings')
      ->set('subject', $form_state->getValue('subject'))
      ->set('message_text', $form_state->getValue('message_text'))
      ->save();
  }

  /**
   * {@inheritDoc}
   */
  protected function getEditableConfigNames() {
    return ['custom_queue.settings'];
  }

}
