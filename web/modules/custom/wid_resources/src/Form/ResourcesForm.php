<?php

namespace Drupal\wid_resources\Form;

use Drupal\wid_custom_twig\CustomTwigExtension;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an resources form.
 */
class ResourcesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wid_resources_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#prefix'] = '<div class="container wid-resources-form">';
    $form['#suffix'] = '</div>';
    $form['#attributes']['enctype'] = 'multipart/form-data';
    $resources = CustomTwigExtension::loadVocabularyTerm('resources');
    $option = [];
    foreach ($resources as $key => $value) {
      $option[$value["id"]] = $value["name"];
    }
    if ($option) {
      unset($option[27], $option[28], $option[33], $option[73]);
      ksort($option);
    }
    $form['wid_resources_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter the title of your content'),
      '#required' => 'true',
    ];
    $form['wid_resources_type'] = [
      '#type' => 'select',
      '#title' => $this
        ->t('Select Category'),
      '#options' => $option,
      '#empty_option' => t("- Select a value -"),
      '#required' => 'true',
    ];
    $form['wid_resources_body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Add a short description'),
      '#required' => 'true',
    ];
    $validators = [
      'file_validate_extensions' => ['docx jpg jpeg gif png txt doc xls pdf ppt pps odt ods odp'],
    ];
    $form['wid_resources_document'] = [
      '#type' => 'managed_file',
      '#name' => 'wid_resources_document',
      '#title' => t('Upload your document'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://' . date('Y-m') . '/',
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
