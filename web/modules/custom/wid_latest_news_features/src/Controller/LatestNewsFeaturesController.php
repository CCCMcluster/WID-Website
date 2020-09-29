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
    $feature_news_query = Drupal::entityQuery('node');
    $feature_news_query->condition('type', 'news')->sort('created', 'DESC')
      ->condition('status', 1)
      ->range(0, 5);
    $node_ids = $feature_news_query->execute();
    $feature_news = Drupal::entityTypeManager()->getStorage('node')->loadMultiple($node_ids);
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
    $news = Drupal::entityTypeManager()->getStorage('node')->loadMultiple($node_ids);
    return $news;
  }
}
