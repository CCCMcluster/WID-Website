<?php

namespace Drupal\wid_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Mail\MailFormatHelper;
use Drupal\taxonomy\Entity\Term;
use Drupal;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Url;

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
    catch (\Exception $e) {
      $response = NULL;
      $geoData = NULL;
    }
    $serializer = Drupal::service('serializer');
    $mapData = [];
    $index = 0;
    $country_reports = Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => 'country_report_overview']);
    foreach ($country_reports as $key => $node) {
      $mapData[$index]['iso_2'] = $node->get('field_report_overview_country')
        ->getValue()[0]['value'];
      if ($geoData) {
        foreach ($geoData->features as $features) {
          if ($node->get('field_report_overview_country')->getValue()[0]['value'] == $features->iso_2) {
            $mapData[$index]['centroid'] = $features->properties->centroid;
          }
        }
      }
      $index++;
    }
    $data = [];
    $data['mapData'] = $serializer->serialize($mapData, 'json', ['plugin_id' => 'entity']);
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public static function getReportByCountry(Request $request) {
    $iso_2 = Drupal::request()->query->get('iso');
    if (isset($iso_2)) {
      $tid = Drupal::entityQuery('taxonomy_term')
        ->condition('field_report_overview_country', $iso_2)
        ->execute();
      $node_ids = Drupal::entityQuery('node')
        ->condition('type', 'wid_map')
        ->condition('status', 1)
        ->condition('field_map_country', $tid)
        ->execute();
      if ($node_ids) {
        $node_ids_other = Drupal::entityQuery('node')
          ->condition('type', 'wid_map')
          ->condition('status', 1)
          ->condition('field_map_country', $tid, '>')
          ->range(0, 2)
          ->execute();
        if (isset($node_ids_other)) {
          $node_ids = array_merge($node_ids, $node_ids_other);
        }
      }
      $nodes = Node::loadMultiple($node_ids);
      $mapData = [];
      $index = 0;
      foreach ($nodes as $key => $node) {
        $tid = $node->get('field_map_country')->getValue()[0]['target_id'];
        $term_country = Term::load($tid);
        $mapData[$index]['id'] = $index + 1;
        $mapData[$index]['title'] = $term_country->get('name')->value;
        $mapData[$index]['current_crisis'] = $node->get('field_current_crisis')
          ->getValue()[0]['value'];
        $mapData[$index]['camp_location'] = $node->get('field_camp_location')
          ->getValue()[0]['value'];
        $current_crisis_key = $node->get('field_current_crisis')
          ->getValue()[0]['value'];
        $current_crisis = $node->field_current_crisis->getSetting('allowed_values')[$current_crisis_key];
        $implementation_year = $node->get('field_implementation_year')
          ->getValue()[0]['value'];;
        $mapData[$index]['current_crisis'] = $current_crisis . t(' in ') . $implementation_year;
        $mapData[$index]['key_activities'] = $node->get('field_map_key_activities')
          ->getValue()[0]['value'];
        $mapData[$index]['wpp_agencies'] = t("Agencies of WPP: ") . implode(", ",
            array_column($node->get('field_wpp_agencies')
              ->getValue(), 'value'));
        $mapData[$index]['iso_2'] = $term_country->get('field_report_overview_country')->value;
        $mapData[$index]['url'] = Url::fromRoute('entity.node.canonical', ['node' => $node->id()])
          ->toString();
        $index++;
      }
      return new JsonResponse($mapData);
    }
    return new JsonResponse([["status", "404 Not Found"]]);
  }

}
