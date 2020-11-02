<?php

namespace Drupal\wid_resources\Form;

use Drupal\wid_custom_twig\CustomTwigExtension;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

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
    $form['#prefix'] .= '<div class="wid-resources-form-title"><h2>Add Content</h2></div>';
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
    honeypot_add_form_protection($form, $form_state, [
      'honeypot',
      'time_restriction',
    ]);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('wid_resources_title');
    $resources_type = $form_state->getValue('wid_resources_type');
    $body = $form_state->getValue('wid_resources_body');
    if (empty($title)) {
      $form_state->setErrorByName('wid_resources_title', $this->t('Title is required.'));
    }
    if (empty($resources_type)) {
      $form_state->setErrorByName('wid_resources_type', $this->t('Category is required.'));
    }
    if (empty($body)) {
      $form_state->setErrorByName('wid_resources_body', $this->t('Description is required.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('wid_resources_title');
    $resources_type = $form_state->getValue('wid_resources_type');
    $body = $form_state->getValue('wid_resources_body');
    $document = $form_state->getValue('wid_resources_document', 0);
    $media_id = NULL;
    if (isset($document[0]) && !empty($document[0])) {
      $file = File::load($document[0]);
      $media = Media::create([
        'bundle' => 'document',
        'uid' => \Drupal::currentUser()->id(),
        'field_media_document' => [
          'target_id' => $file->id(),
        ],
      ]);
      $media->setName($file->getFilename())->setPublished(TRUE)->save();
      $media_id = $media->id();
    }
    $node = \Drupal::entityTypeManager()->getStorage('node')->create([
      'type' => 'resources',
      'title' => $title,
      'body' => $body,
      'field_resources_type' => $resources_type,
      'field_resources_attachment_type' => 'Document',
      'field_resources_document' => ['target_id' => $media_id],
    ]);
    $node->save();
    $form_state->setRedirect(
      'entity.node.canonical',
      ['node' => $node->id()]
    );
  }

}
