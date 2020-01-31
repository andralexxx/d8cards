<?php

namespace Drupal\Tests\custom_libraries\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests that the CSS, JS and YAML files are in appropriate subdirectories.
 *
 * @group custom_libraries
 */
class FilesExistsTest extends TestCase {

  /**
   * Provide list of files to check.
   *
   * @return array[]
   *   An array of strings, suitable as a data provider. Strings are paths
   *   to files.
   */
  public function provideFilePaths() {
    $module_path = realpath(__DIR__ . '/../../..');

    return [
      ["$module_path/css/custom-libraries-theme.css"],
      ["$module_path/js/custom-libraries.js"],
      ["$module_path/custom_libraries.libraries.yml"],
    ];
  }

  /**
   * Check whether file exists.
   *
   * @param string $file_path
   *   The path to test.
   *
   * @dataProvider provideFilePaths
   */
  public function testFilePaths($file_path) {
    $this->assertFileExists($file_path, "$file_path file exists.");
  }

}
