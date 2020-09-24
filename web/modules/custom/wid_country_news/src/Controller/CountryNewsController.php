<?php

namespace Drupal\wid_country_news\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal;
use Drupal\views\Views;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Class CountryNewsController.
 *
 * @package Drupal\wid_country_news\Controller
 */
class CountryNewsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function getCountryNews($country_iso = 'AF') {
    $view = Views::getView('news_content');
    $view->setDisplay('country_news_rest_export');
    $view->setArguments([$country_iso]);
    $view->execute();
    $view_result = $view->result;
    $news = [];
    $index = 0;
    foreach ($view_result as $data) {
      $entity = $data->_entity;
      $news_media_tid = $entity->field_featured_media->target_id;
      $news_media_url = NULL;
      if ($news_media_tid) {
        $news_media_id = Media::load($news_media_tid);
        $news_media_fid = $news_media_id->getSource()
          ->getSourceFieldValue($news_media_id);
        $news_media_file = File::load($news_media_fid);
        $news_media_url = $news_media_file->createFileUrl();
      }
      $title = $entity->getTitle();
      $body = $entity->get('body')->summary;
      $created_date = Drupal::service('date.formatter')
        ->format($entity->get('created')->value, 'custom', 'm/d/Y');
      $news[$index] = [
        'title' => $title,
        'body' => $body,
        'url' => $news_media_url,
        'created_date' => $created_date,
      ];
      $index++;
    }
    return $news;
  }

}
