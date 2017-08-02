<?php

namespace Drupal\islandora_xml_import\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class XmlImportFormatForm.
 */
class XmlImportFormatForm extends EntityForm {

  /**
   * @var array
   * Associative array of fields to choose from.
   */
  protected $fields;

  /**
   * {@inheritdoc}
   */
  protected function init(FormStateInterface $form_state)
  {
    parent::init($form_state);

    // Just do the entity query once at the beginning instead of every page load.
    $this->fields = \Drupal::entityQuery('field_storage_config')
      ->condition('type', 'entity_reference', '<>')
      ->condition('type', 'image', '<>')
      ->condition('type', 'file', '<>')
      ->condition('type', 'comment', '<>')
      ->execute();
  }
 
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $xml_import_format = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $xml_import_format->label(),
      '#description' => $this->t("Label for the XML Import Format."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $xml_import_format->id(),
      '#machine_name' => [
        'exists' => '\Drupal\islandora_xml_import\Entity\XmlImportFormat::load',
      ],
      '#disabled' => !$xml_import_format->isNew(),
    ];

    $form['xpaths'] = [
      '#type' => 'table',
      '#tree' => TRUE,
      '#prefix' => '<div id="xpaths-wrapper">',
      '#suffix' => '</div>',
      '#header' => [
        'field' => t('Field'),
        'xpath' => t('XPath'),
        'operations' => t('Operations'),
      ],
    ];

    $values = $form_state->getValue('xpaths');
    if (empty($values)) {
      $values = $xml_import_format->getXPaths();
    }

    foreach ($values as $k => $v) {
      $form['xpaths'][$k] = [
        'field' => [
          '#type' => 'select',
          '#options' => $this->fields,
          '#default_value' => $v['field'],
          '#required' => TRUE,
        ],
        'xpath' => [
          '#type' => 'textfield',
          '#default_value' => $v['xpath'],
          '#required' => TRUE,
        ],
        'operations' => [
          '#type' => 'submit',
          '#name' => "remove-xpath-$k",
          '#value' => t('Remove'),
          '#submit' => array('::removeXPath'),
          '#ajax' => [
            'callback' => '::ajaxCallback',
            'wrapper' => 'xpaths-wrapper',
          ],
        ],
      ];
    }

    $form['add'] = [
      '#type' => 'submit',
      '#name' => 'add-xpath',
      '#value' => t('Add XPath'),
      '#submit' => array('::addXPath'),
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'wrapper' => 'xpaths-wrapper',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $xml_import_format = $this->entity;

    // Strip array keys before saving.  Unsetting them in the ajax callback
    // causes them to appear in the config entity's yaml.
    $xml_import_format->setXPaths(
      array_values($form_state->getValue('xpaths'))
    );

    $status = $xml_import_format->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label XML Import Format.', [
          '%label' => $xml_import_format->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label XML Import Format.', [
          '%label' => $xml_import_format->label(),
        ]));
    }
    $form_state->setRedirectUrl($xml_import_format->toUrl('collection'));
  }

  /**
   * Callback for both ajax operations.
   *
   * Selects and returns the table's form element.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function ajaxCallback(array &$form, FormStateInterface $form_state) {
    $xpaths = $form_state->get('xpaths');
    return $form['xpaths'];
  }

  /**
   * Submit handler for the "add-xpath" button.
   *
   * Adds a new empty xpath and causes a rebuild.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function addXPath(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('xpaths');
    $values[] = [
      'field' => NULL,
      'xpath' => '',
    ];
    $form_state->setValue('xpaths', $values);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove-xpath" buttons.
   *
   * Removes a row from the xpath tables and causes a rebuild.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function removeXPath(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $values = $form_state->getValue('xpaths');

    // Remove the row of the triggering element.
    unset($values[$trigger['#array_parents'][1]]);

    $form_state->setValue('xpaths', $values);
    $form_state->setRebuild();
  }

}
