<?php

namespace test\phpunit\Deploy;

use Drupal\va_gov_backend\Deploy\DeployService;

/**
 * Mock DeployService since we can't mock static methods.
 */
class DeployServiceMock extends DeployService {

  /**
   * {@inheritDoc}
   */
  public static function deployPlugins(): array {
    return [DeployPluginTest::class];
  }

}
