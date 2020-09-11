<?php

namespace Drupal\wid_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class MapController.
 *
 * @package Drupal\wid_map\Controller
 */
class MapController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function getData() {
    $serializer = Drupal::service('serializer');
    $node_ids = Drupal::entityQuery('node')
      ->condition('type', 'news')
      ->condition('status', 1)
      ->condition('field_countries', '', '<>')
      ->execute();
    $nodes = Node::loadMultiple($node_ids);
    $mapData = [];
    $index = 0;
    foreach ($nodes as $key => $node) {
      $mapData[$index]['iso_2'] = $node->get('field_countries')->getValue()[0]['value'];
      $index++;
    }
    $data = $serializer->serialize($mapData, 'json', ['plugin_id' => 'entity']);
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public static function getReportByCountry(Request $request) {
    $iso_2 = Drupal::request()->query->get('iso');
    if (isset($iso_2)) {
      $node_ids = Drupal::entityQuery('node')
        ->condition('type', 'news')
        ->condition('status', 1)
        ->condition('field_countries', '', '<>')
        ->condition('field_countries', $iso_2)
        ->execute();
      $nodes = Node::loadMultiple($node_ids);
      $country_manager = Drupal::service('country_manager');
      $mapData = [];
      $index = 0;
      foreach ($nodes as $key => $node) {
        $mapData[$index]['id'] = $index + 1;
        $mapData[$index]['title'] = $node->get('title')->getValue()[0]['value'];
        $mapData[$index]['body'] = $node->get('body')->getValue()[0]['value'];
        $mapData[$index]['country'] = $country_manager->getList()[$node->get('field_countries')->getValue()[0]['value']]->__toString();
        $mapData[$index]['iso_2'] = $node->get('field_countries')->getValue()[0]['value'];
        $index++;
      }
      return new JsonResponse($mapData);
    }
    return new JsonResponse([["status", "404 Not Found"]]);
  }

}
