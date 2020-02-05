<?php

namespace Drupal\custom_content_entity_type\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Contact type entity.
 *
 * @ConfigEntityType(
 *   id = "contact_type",
 *   label = @Translation("Contact type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\custom_content_entity_type\ContactTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\custom_content_entity_type\Form\ContactTypeForm",
 *       "edit" = "Drupal\custom_content_entity_type\Form\ContactTypeForm",
 *       "delete" = "Drupal\custom_content_entity_type\Form\ContactTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_content_entity_type\ContactTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "contact_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "contact",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/contact_types/{contact_type}",
 *     "add-form" = "/admin/structure/contact_types/add",
 *     "edit-form" = "/admin/structure/contact_types/{contact_type}/edit",
 *     "delete-form" = "/admin/structure/contact_types/{contact_type}/delete",
 *     "collection" = "/admin/structure/contact_types"
 *   }
 * )
 */
class ContactType extends ConfigEntityBundleBase implements ContactTypeInterface {

  /**
   * The Contact type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Contact type label.
   *
   * @var string
   */
  protected $label;

}
