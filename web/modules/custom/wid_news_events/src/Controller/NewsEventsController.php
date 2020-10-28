<?php

namespace Drupal\wid_news_events\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal;
use DateTime;

/**
 * Class NewsEventsController.
 *
 * @package Drupal\wid_news_events\Controller
 */
class NewsEventsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function getUpcomingEvent() {
    $event = NULL;
    $timezone = date_default_timezone_get();
    $start = new DateTime('now', new \DateTimezone($timezone));
    $start->modify('-1 day');
    $start->setTime(00, 00);
    $event_query = Drupal::entityQuery('node');
    $event_query
      ->condition('type', 'events')
      ->condition('field_event_date', $start->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=')
      ->condition('status', 1)
      ->sort('field_event_date', 'ASC');
    $event_results = $event_query->execute();
    $events = Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($event_results);
    return $events;
  }

  /**
   * {@inheritdoc}
   */
  public static function getNews() {
    $db = \Drupal::database();
    $report_query = $db->select('flagging', 'f')
      ->fields('f', ['entity_id'])
      ->condition('f.flag_id', 'promote_to_event_news')
      ->condition('f.entity_type', 'node')
      ->orderBy('f.created', 'DESC')
      ->range(0, 1)
      ->execute()
      ->fetchAll();
    if ($report_query) {
      $nid = $report_query[0]->entity_id;
      $reports = Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($nid);
      return $reports;
    }
    $report_query = Drupal::entityQuery('node');
    $report_query->condition('type', 'news')->sort('created', 'DESC')
      ->condition('status', 1)
      ->range(0, 1);
    $nid = $report_query->execute();
    if ($nid) {
      $nid = array_values($nid);
      $reports = Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($nid[0]);
      return $reports;
    }
    return NULL;
  }

}
