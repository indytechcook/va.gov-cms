<?php

namespace Drupal\va_gov_backend\Deploy\Plugin;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
use Drupal\va_gov_content_export\Archive\ArchiveArgs;
use Drupal\va_gov_content_export\Archive\ArchiveArgsFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * CMS Export plugin.
 */
class CMSExport implements DeployPluginInterface {

  /**
   * A stream wrapper.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperInterface
   */
  protected $streamWrapperInstance;

  /**
   * {@inheritDoc}
   */
  public function match(Request $request): bool {
    $current_path = $request->getPathInfo();
    if ($current_path === '/cms-export/content') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function run(Request $request, string $app_root, string $site_path) {
    $this->registerStreamWrapper();
    if ($this->fileExists()) {
      $url = $this->getUrl();

      return RedirectResponse::create($url);
    }
  }

  /**
   * Register the stream wrapper to be used later.
   */
  protected function registerStreamWrapper() : void {
    $archiveArgs = $this->getArchiveArgs();
    $output_uri = $archiveArgs->getOutputPath();
    $this->streamWrapperInstance = new PublicStream();
    $this->streamWrapperInstance->setUri($output_uri);
  }

  /**
   * Get the Arguments used to find the tar archive.
   *
   * @return \Drupal\va_gov_content_export\Archive\ArchiveArgs
   *   The arguments used to find the tar archive.
   */
  protected function getArchiveArgs() : ArchiveArgs {
    $archiveArgsFactory = new ArchiveArgsFactory();
    return $archiveArgsFactory->createContentArgs();
  }

  /**
   * Does the tar archive file exist?
   *
   * @return bool
   *   Does the file exist.
   */
  protected function fileExists() : bool {
    $file_name = $this->getFilePath();
    return file_exists($file_name);
  }

  /**
   * Get the file path to the tar archive.
   *
   * @return null|string
   *   Either the local path to the file or null.
   */
  protected function getFilePath() : ?string {
    $streamWrapper = $this->getStreamWrapper();
    if ($streamWrapper) {
      return $streamWrapper->realpath();
    }

    return NULL;
  }

  /**
   * Get the stream wrapper instance.
   *
   * @reutrn Drupal\Core\StreamWrapper\StreamWrapperInterface
   *  The stream wrapper.
   */
  protected function getStreamWrapper() : ?StreamWrapperInterface {
    return $this->streamWrapperInstance;
  }

  /**
   * Get the URl to the file.
   *
   * @return string
   *   The url.
   */
  protected function getUrl() : string {
    return $this->getStreamWrapper()->getExternalUrl();
  }

}
