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
    $report_query = Drupal::entityQuery('node');
    $report_query->condition('type', 'news')->sort('created', 'DESC')
      ->condition('status', 1)
      ->range(0, 1);
    $nids = $report_query->execute();
    $reports = Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nids);
    return $reports;
  }

}
