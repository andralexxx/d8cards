<?php

namespace Drupal\custom_content_entity_type\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ContactTypeForm.
 */
class ContactTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $contact_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $contact_type->label(),
      '#description' => $this->t("Label for the Contact type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $contact_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\custom_content_entity_type\Entity\ContactType::load',
      ],
      '#disabled' => !$contact_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $contact_type = $this->entity;
    $status = $contact_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Contact type.', [
          '%label' => $contact_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Contact type.', [
          '%label' => $contact_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($contact_type->toUrl('collection'));
  }

}
