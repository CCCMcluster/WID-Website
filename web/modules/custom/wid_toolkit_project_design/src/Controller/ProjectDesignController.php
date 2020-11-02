<?php

namespace Drupal\wid_toolkit_project_design\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal;
use Drupal\views\Views;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Class ProjectDesignController.
 *
 * @package Drupal\wid_toolkit_project_design\Controller
 */
class ProjectDesignController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function getProjectDesign() {
    $view = Views::getView('toolkit_project_design');
    $view->setDisplay('project_design_workshop');
    $view->execute();
    $view_result = $view->result;
    $projectDesignWorkshop = [];
    $index = 0;
    foreach ($view_result as $data) {
      $entity = $data->_entity;
      $toolkit_tid = $entity->get('field_toolkit')->target_id;
      $toolkit_attachment_type = $entity->get('field_toolkit_attachment_type')->value;
      $cover_image_tid = $entity->field_toolkit_cover_img->target_id;
      $cover_image = [];
      if ($cover_image_tid) {
        $cover_image_tid = Media::load($cover_image_tid);
        $cover_image_fid = $cover_image_tid->getSource()
          ->getSourceFieldValue($cover_image_tid);
        $cover_image_file = File::load($cover_image_fid);
        $cover_image_url = $cover_image_file->createFileUrl();
        $cover_image = [
          'url' => $cover_image_url,
          'alt' => $entity->get('field_toolkit_cover_img')->entity->field_media_image->alt,
        ];
      }
      $document = [];
      $documentLink = NULL;
      if ($toolkit_attachment_type == 'Document') {
        $document_tid = $entity->field_toolkit_document->target_id;
        $document_tid = Media::load($document_tid);
        $document_fid = $document_tid->getSource()
          ->getSourceFieldValue($document_tid);
        $document_file = File::load($document_fid);
        $document_file_size = $document_file->getSize();
        $document_file_type = pathinfo($document_file->getFilename(), PATHINFO_EXTENSION);
        $document_file_type_icon = file_exists(DRUPAL_ROOT . "/themes/custom/wid/images/icons/$document_file_type.svg");
        if ($document_file_type_icon == FALSE) {
          $document_file_type = "file";
        }
        $document_url = $document_file->createFileUrl();

        $document = [
          'fid' => $document_file->id(),
          'url' => $document_url,
          'type' => $document_file_type,
          'size' => round($document_file_size / 1024),
        ];
      }
      elseif ($toolkit_attachment_type == 'Link') {
        $documentLink = $entity->field_toolkit_document_link->uri;
      }
      $projectDesignWorkshop[$toolkit_tid][$index] = [
        'nid' => $entity->id(),
        'entity_type' => $entity->getEntityType()->id(),
        'title' => $entity->getTitle(),
        'description' => $entity->get('body')->value,
        'cover_image' => $cover_image,
        'document' => $document,
        'document_link' => $documentLink,
        'created_date' => Drupal::service('date.formatter')
          ->format($entity->get('created')->value, 'custom', 'm/d/y'),
      ];
      $index++;
    }
    ksort($projectDesignWorkshop);
    return $projectDesignWorkshop;
  }

}
