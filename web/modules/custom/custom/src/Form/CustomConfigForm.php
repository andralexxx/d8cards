<?php

namespace Drupal\custom\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CustomConfigForm.
 */
class CustomConfigForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'custom.customconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('custom.customconfig');
    $form['custom_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Some text'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('custom_text'),
    ];
    $form['custom_select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select an option'),
      '#options' => ['one' => $this->t('one'), 'two' => $this->t('two'), 'three' => $this->t('three'), 'four' => $this->t('four'), 'five' => $this->t('five'), 'six' => $this->t('six')],
      '#size' => 5,
      '#default_value' => $config->get('custom_select'),
    ];
    $form['custom_radios'] = [
      '#type' => 'radios',
      '#title' => $this->t('Are you agree'),
      '#options' => ['Yes' => $this->t('Yes'), 'No' => $this->t('No')],
      '#default_value' => $config->get('custom_radios'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('custom.customconfig')
      ->set('custom_text', $form_state->getValue('custom_text'))
      ->set('custom_select', $form_state->getValue('custom_select'))
      ->set('custom_radios', $form_state->getValue('custom_radios'))
      ->save();
  }

}
