<?php

namespace Drupal\wid_custom_twig;

use Twig_SimpleFunction;
use Twig_ExtensionInterface;
use Drupal;
use Twig_Extension;
use Drupal\node\Entity\Node;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\block\Entity\Block;

/**
 * Extend Drupal's Twig_Extension class.
 */
class CustomTwigExtension extends Twig_Extension {

  /**
   * Name of twig.
   */
  public function getName() {
    return 'wid_custom_twig.CustomTwigExtension';
  }

  /**
   * Return your custom twig function to Drupal.
   */
  public function getFunctions() {
    $functions = [
      new Twig_SimpleFunction('load_vocabulary_term', [
        $this,
        'loadVocabularyTerm',
      ]),
      new Twig_SimpleFunction('load_tax_term', [$this, 'loadTaxTerm']),
      new Twig_SimpleFunction('media_file_url', [$this, 'mediaFileUrl']),
      new Twig_SimpleFunction('get_file_field_uri', [$this, 'fileFieldUri']),
      new Twig_SimpleFunction('media_file_type', [$this, 'mediaFileType']),
      new Twig_SimpleFunction('country_name', [$this, 'getCountryName']),
      new Twig_SimpleFunction('get_social_media_links', [
        $this,
        'getSocialMediaLinks',
      ]),
    ];
    return $functions;
  }

  /**
   * Returns tree of the vocabulary.
   *
   * @param string $vid
   *   Vid of the vocabulary.
   *
   * @return array
   *   Tree of vocabulary.
   */
  public static function loadVocabularyTerm($vid) {
    $terms = Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    foreach ($terms as $term) {
      $term_data[] = [
        'id' => $term->tid,
        'name' => $term->name,
      ];
    }
    return $term_data;
  }

  /**
   * Returns taxonomy term.
   *
   * @param string $tid
   *   Tid of the taxonomy.
   *
   * @return array
   *   Taxonomy term.
   */
  public static function loadTaxTerm($tid) {
    $taxonomy_term = Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($tid);
    return $taxonomy_term;
  }

  /**
   * Returns the file URL from a media entity.
   *
   * @param string $mid
   *   The media entity target id.
   * @param string $field
   *   The media field.
   *
   * @return string
   *   The file url.
   */
  public function mediaFileUrl($mid, $field = 'field_media_image') {
    if (!$mid) {
      return NULL;
    }
    $media = Media::load($mid);
    $fid = $media->$field->target_id;
    $file = File::load($fid);
    if ($file) {
      $url = $file->createFileUrl();
      return $url;
    }
    return NULL;
  }

  /**
   * Returns the file type from a media entity.
   *
   * @param string $mid
   *   The media entity target id.
   * @param string $field
   *   The media field.
   *
   * @return string
   *   The file type.
   */
  public function mediaFileType($mid, $field = 'field_media_file') {
    if (!$mid) {
      return NULL;
    }
    $media = Media::load($mid);
    $fid = $media->$field->target_id;
    $file = File::load($fid);
    if ($file) {
      $type = $file->getFilename();
      $ext = pathinfo($type, PATHINFO_EXTENSION);
      return $ext;
    }
    return NULL;
  }

  /**
   * Returns social media links.
   *
   * @param string $block_id
   *   Block ID.
   *
   * @return array
   *   List of social media links.
   */
  public function getSocialMediaLinks($block_id) {
    $block = Block::load($block_id);
    $social_media = [];
    $host = Drupal::request()->getHost();
    $social_media_platforms = [
      'rss' => $host . '/',
      'email' => 'mailto:',
      'youtube_channel' => 'https://www.youtube.com/channel/',
      'website' => '',
      'instagram' => 'https://www.instagram.com/',
      'vimeo' => 'https://www.vimeo.com/',
      'bitbucket' => 'https://bitbucket.org/',
      'drupal' => 'https://www.drupal.org/u/',
      'tumblr' => '',
      'contact' => $host . '/',
      'behance' => 'https://www.behance.net/',
      'flickr' => 'https://www.flickr.com/photos/',
      'linkedin' => 'https://www.linkedin.com/',
      'twitter' => 'https://www.twitter.com/',
      'xing' => 'https://www.xing.com/',
      'youtube' => 'https://www.youtube.com/',
      'pinterest' => 'https://www.pinterest.com/',
      'whatsapp' => 'https://api.whatsapp.com/send?phone=',
      'vkontakte' => 'https://vk.com/',
      'facebook' => 'https://www.facebook.com/',
      'googleplus' => 'https://plus.google.com/',
      'slideshare' => 'https://www.slideshare.net/',
    ];
    if ($block) {
      $platforms = $block->get('settings')['platforms'];
      $link_attributes = $block->get('settings')['link_attributes'];
      foreach ($platforms as $platform => $platform_detail) {
        if (!empty($platform_detail['value'])) {
          $platform_detail['target'] = $link_attributes['target'];
          $platform_detail['url'] = $social_media_platforms[$platform] . trim($platform_detail['value']);
          $social_media[$platform] = $platform_detail;
        }
      }
      return $social_media;
    }
    else {
      return NULL;
    }
  }

  /**
   * Returns country name.
   *
   * @param string $country_code
   *   Country Code.
   *
   * @return string
   *   Country Name.
   */
  public function getCountryName($country_code) {
    if ($country_code) {
      $country = Drupal::service('country_manager')
        ->getList()[strtoupper($country_code)]->__toString();
      return $country;
    }
    else {
      return NULL;
    }
  }

  /**
   * Returns set or default image uri for a file image field (if either exist).
   *
   * @param string $entity
   *   The entity.
   * @param string $fieldName
   *   The field name.
   *
   * @return string
   *   file uri.
   */
  public function fileFieldUri($entity, $fieldName) {
    $image_uri = NULL;
    if ($entity->hasField($fieldName)) {
      try {
        $field = $entity->{$fieldName};
        if ($field && $field->target_id) {
          $file = File::load($field->target_id);
          if ($file) {
            $image_uri = $file->getFileUri();
          }
        }
      }
      catch (Exception $e) {
        Drupal::logger('get_image_uri')->notice($e->getMessage(), []);
      }
      if (is_null($image_uri)) {
        try {
          $field = $entity->get($fieldName);
          if ($field) {
            $default_image = $field->getSetting('default_image');

            if ($default_image && $default_image['uuid']) {
              $entity_repository = Drupal::service('entity.repository');
              $defaultImageFile = $entity_repository->loadEntityByUuid('file', $default_image['uuid']);
              if ($defaultImageFile) {
                $image_uri = $defaultImageFile->getFileUri();
              }
            }
          }
        }
        catch (Exception $e) {
          Drupal::logger('get_image_uri')->notice($e->getMessage(), []);
        }
      }
    }
    return $image_uri;
  }

}
