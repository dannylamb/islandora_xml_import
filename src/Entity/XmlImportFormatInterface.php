<?php

namespace Drupal\islandora_xml_import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining XML Import Format entities.
 */
interface XmlImportFormatInterface extends ConfigEntityInterface {

  /**
   * XPaths getter.
   *
   * @return array
   */
  public function getXPaths();

  /**
   * XPaths setter.
   *
   * @param array $xpaths
   *   The xpaths to set.
   */
  public function setXPaths(array $xpaths);

}
