<?php

namespace Drupal\islandora_xml_import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the XML Import Format entity.
 *
 * @ConfigEntityType(
 *   id = "xml_import_format",
 *   label = @Translation("XML Import Format"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\islandora_xml_import\XmlImportFormatListBuilder",
 *     "form" = {
 *       "add" = "Drupal\islandora_xml_import\Form\XmlImportFormatForm",
 *       "edit" = "Drupal\islandora_xml_import\Form\XmlImportFormatForm",
 *       "delete" = "Drupal\islandora_xml_import\Form\XmlImportFormatDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\islandora_xml_import\XmlImportFormatHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "xml_import_format",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/xml_import_format/{xml_import_format}",
 *     "add-form" = "/admin/structure/xml_import_format/add",
 *     "edit-form" = "/admin/structure/xml_import_format/{xml_import_format}/edit",
 *     "delete-form" = "/admin/structure/xml_import_format/{xml_import_format}/delete",
 *     "collection" = "/admin/structure/xml_import_format"
 *   }
 * )
 */
class XmlImportFormat extends ConfigEntityBase implements XmlImportFormatInterface {

  /**
   * The XML Import Format ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The XML Import Format label.
   *
   * @var string
   */
  protected $label;

  /**
   * List of XPaths for the import schema.
   *
   * @var array
   */
  protected $xpaths;

  /**
   * {@inheritdoc}
   */
  public function getXPaths()
  {
    return $this->xpaths;
  }

  /**
   * {@inheritdoc}
   */
  public function setXPaths(array $xpaths)
  {
    $this->xpaths = $xpaths;
  }

}
