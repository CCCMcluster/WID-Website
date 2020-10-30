<?php

namespace Drupal\wid_latest_news_features\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal;

/**
 * Class LatestNewsFeaturesController.
 *
 * @package Drupal\wid_latest_news_features\Controller
 */
class LatestNewsFeaturesController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function getFeatureNews() {
    $db = \Drupal::database();
    $node_ids = $db->select('flagging', 'f')
      ->fields('f', ['entity_id'])
      ->condition('f.flag_id', 'promote_to_feature_news')
      ->condition('f.entity_type', 'node')
      ->orderBy('f.created', 'DESC')
      ->execute()
      ->fetchAll();
    if ($node_ids) {
      $nids = [];
      foreach ($node_ids as $key => $value) {
        $nids[] = $value->entity_id;
      }
      $feature_news = Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadMultiple($nids);
      return $feature_news;
    }
    $feature_news_query = Drupal::entityQuery('node');
    $feature_news_query->condition('type', 'news')->sort('created', 'DESC')
      ->condition('status', 1)
      ->range(0, 5);
    $node_ids = $feature_news_query->execute();
    $feature_news = Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($node_ids);
    return $feature_news;
  }

  /**
   * {@inheritdoc}
   */
  public static function getLatestNews() {
    $news_query = Drupal::entityQuery('node');
    $news_query->condition('type', 'news')->sort('created', 'DESC')
      ->condition('status', 1)
      ->range(0, 5);
    $node_ids = $news_query->execute();
    $news = Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($node_ids);
    return $news;
  }

}
