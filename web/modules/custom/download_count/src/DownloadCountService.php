<?php

namespace Drupal\download_count;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheTagsInvalidator;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\file\FileInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class CountService.
 */
class DownloadCountService {

  /**
   * Logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Request.
   *
   * @var \Symfony\Component\HttpFoundation\Request|null
   */
  protected $request;

  /**
   * Time Service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Flood.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * Translation Manager.
   *
   * @var \Drupal\Core\StringTranslation\TranslationManager
   */
  protected $translationManager;

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * Cache Tags Invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * Config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Constructs a new CountService object.
   */
  public function __construct(
    LoggerInterface $logger,
    RequestStack $request_stack,
    ConfigFactoryInterface $config_factory,
    TimeInterface $time,
    FloodInterface $flood,
    TranslationManager $translation_manager,
    Connection $database,
    CacheTagsInvalidatorInterface $cache_tags_invalidator
  ) {
    $this->logger = $logger;
    $this->request = $request_stack->getCurrentRequest();
    $this->time = $time;
    $this->flood = $flood;
    $this->translationManager = $translation_manager;
    $this->database = $database;
    $this->cacheTagsInvalidator = $cache_tags_invalidator;

    $this->config = $config_factory->get('download_count.settings');
  }

  /**
   * Get config.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   Return config.
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * Check Flood control.
   *
   * @param \Drupal\file\FileInterface $file
   *   File.
   *
   * @return bool
   *   TRUE if the user is allowed to proceed. FALSE if they have exceeded the
   *   threshold and should not be allowed to proceed.
   */
  public function checkFloodControl(FileInterface $file) {

    // Get flood limit.
    $flood_limit = $this->config->get('download_count_flood_limit');

    // Get flood window.
    $flood_window = $this->config->get('download_count_flood_window');

    // Always allow, if there is no flood limit set.
    if ($flood_limit == 0) {
      return TRUE;
    }

    return $this->flood->isAllowed(
      'download_count-fid_' . $file->id(),
      $flood_limit,
      $flood_window
    );

  }

  /**
   * Add Count.
   *
   * @param \Drupal\file\FileInterface $file
   *   File.
   * @param int $uid
   *   User ID.
   * @param string $entity_type
   *   Entity Type.
   * @param int $entity_id
   *   Entity ID.
   */
  public function addCount(FileInterface $file, $uid, $entity_type, $entity_id) {

    // Get timestamp.
    $timestamp = $this->time->getRequestTime();

    // Get Referer.
    $referrer = ($this->request->server->has('HTTP_REFERER')) ? $this->request->server->get('HTTP_REFERER') : t('Direct download');

    // Get IP.
    $ip_address = $this->request->getClientIp();

    $this->database->insert('download_count')
      ->fields([
        'fid' => $file->id(),
        'uid' => $uid,
        'type' => $entity_type,
        'id' => $entity_id,
        'ip_address' => $ip_address,
        'referrer' => $referrer,
        'timestamp' => $timestamp,
      ])->execute();

    // Get flood window.
    $flood_window = $this->config->get('download_count_flood_window');

    // Add flood entry.
    $this->flood->register('download_count-fid_' . $file->id(), $flood_window);

    // Clear any cache item associated with the download count.
    $this->deleteCachedItems($file->id());

    $this->logger->info('%file was downloaded by user #%uid from %ip', [
      '%file' => $file->getFilename(),
      '%uid' => $uid,
      '%ip' => $ip_address,
    ]);
  }

  /**
   * Get Download Count.
   *
   * @param int $fid
   *   File ID.
   * @param string $entity_type
   *   Entity Type.
   * @param int $entity_id
   *   Entity ID.
   *
   * @return int
   *   Download Count.
   */
  public function getDownloadCount($fid, $entity_type, $entity_id) {

    return $this->database->query('SELECT COUNT(fid) from {download_count} where fid = :fid AND type = :type AND id = :id', [
      ':fid' => $fid,
      ':type' => $entity_type,
      ':id' => $entity_id,
    ])->fetchField();
  }

  /**
   * Build download count text.
   *
   * @param int $count
   *   Download count.
   *
   * @return \Drupal\Core\StringTranslation\PluralTranslatableMarkup
   *   Translated text.
   */
  public function buildDownloadCountText($count) {
    return $this->translationManager->formatPlural($count, 'Downloaded 1 time', 'Downloaded @count times');
  }

  /**
   * Build cache.
   *
   * @param int $fid
   *   File ID.
   *
   * @return string
   *   Cache tag.
   */
  public function buildCacheTag($fid) {
    return 'download-count-' . $fid;
  }

  /**
   * Delete cached items.
   *
   * @param int $fid
   *   File ID.
   */
  public function deleteCachedItems($fid) {
    $this->cacheTagsInvalidator->invalidateTags([$this->buildCacheTag($fid)]);
  }

}
