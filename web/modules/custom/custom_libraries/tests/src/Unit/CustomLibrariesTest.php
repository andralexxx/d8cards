<?php

namespace Drupal\Tests\custom_libraries\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Check that libraries.yml file define exactly 2 libraries.
 *
 * One for each of the CSS and JS.
 *
 * @group custom_libraries
 */
class CustomLibrariesTest extends TestCase {

  /**
   * Check that libraries.yml file define exactly 2 libraries.
   *
   * One for each of the CSS and JS.
   */
  public function testDefinedLibraries() {
    $module_path = realpath(__DIR__ . '/../../..');
    $libraries = Yaml::parse(file_get_contents("$module_path/custom_libraries.libraries.yml"));
    $this->assertCount(2, $libraries, 'There are exactly two libraries.');

    // Check CSS library.
    $this->assertNotEmpty($libraries['custom.css'], 'There is "custom-css" library');
    $library = $libraries['custom.css'];
    $this->assertNotEmpty($library['css']['theme'], "CSS library is present.");
    $this->assertCount(1, $library['css']['theme'], "There is only one file in CSS library.");

    // Check JS library.
    $this->assertNotEmpty($libraries['custom.js'], 'There is "custom-js" library');
    $library = $libraries['custom.js'];
    $this->assertNotEmpty($library['js'], "JS library is present.");
    $this->assertCount(1, $library['js'], "There is only one file in JS library.");
  }

}
