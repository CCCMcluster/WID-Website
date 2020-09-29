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
    $client = \Drupal::httpClient();
    $url = "https://gist.githubusercontent.com/timilsinabishal/1df12bb0ce3afe5dbdd6081f89513cae/raw/ece2a05253c7e86a43d8f3e5ea4841cd79b59299/world-admin-0.geojson";
    try {
      $request = $client->get($url);
      $response = $request->getBody()->getContents();
      $geoData = json_decode($response);
    }
    catch (RequestException $e) {
      $response = NULL;
      $geoData = NULL;
    }
    $serializer = Drupal::service('serializer');
    $node_ids = Drupal::entityQuery('node')
      ->condition('type', 'wid_reports')
      ->condition('status', 1)
      ->condition('field_report_country', '', '<>')
      ->execute();
    $nodes = Node::loadMultiple($node_ids);
    $mapData = [];
    $index = 0;
    foreach ($nodes as $key => $node) {
      $mapData[$index]['iso_2'] = $node->get('field_report_country')
        ->getValue()[0]['value'];
      if ($geoData) {
        foreach ($geoData->features as $features) {
          if ($node->get('field_report_country')->getValue()[0]['value'] == $features->iso_2) {
            $mapData[$index]['centroid'] = $features->properties->centroid;
          }
        }
      }
      $index++;
    }
    $data = [];
    $data['mapData'] = $serializer->serialize($mapData, 'json', ['plugin_id' => 'entity']);
    $data['geoJson'] = $response;
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public static function getReportByCountry(Request $request) {
    $iso_2 = Drupal::request()->query->get('iso');
    if (isset($iso_2)) {
      $node_ids = Drupal::entityQuery('node')
        ->condition('type', 'wid_reports')
        ->condition('status', 1)
        ->condition('field_report_country', '', '<>')
        ->condition('field_report_country', $iso_2)
        ->execute();
      $nodes = Node::loadMultiple($node_ids);
      $country_manager = Drupal::service('country_manager');
      $terms = Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree('country_report_overview');
      foreach ($terms as $term) {
        $country = Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->load($term->tid);
        $country_iso = $country->field_report_overview_country->value;
        $country_name = $country->getName();
        $term_data[$country_iso] = $country_name;
      }
      $mapData = [];
      $index = 0;
      foreach ($nodes as $key => $node) {
        $mapData[$index]['id'] = $index + 1;
        $mapData[$index]['title'] = $node->get('title')->getValue()[0]['value'];
        $mapData[$index]['body'] = $node->get('body')->getValue()[0]['value'];
        if ($term_data[$node->get('field_report_country')
          ->getValue()[0]['value']]) {
          $mapData[$index]['country'] = $term_data[$node->get('field_report_country')
            ->getValue()[0]['value']];
        }
        else {
          $mapData[$index]['country'] = $country_manager->getList()[$node->get('field_report_country')
            ->getValue()[0]['value']]->__toString();
        }
        $mapData[$index]['iso_2'] = $node->get('field_report_country')
          ->getValue()[0]['value'];
        $index++;
      }
      return new JsonResponse($mapData);
    }
    return new JsonResponse([["status", "404 Not Found"]]);
  }

}
