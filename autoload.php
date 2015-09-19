<?php

/**
 * @file
 * Includes the autoloader created by Composer.
 *
 * This file can be edited to change the autoloader if you are managing a
 * project's dependencies using Composer. If Drupal code requires the
 * autoloader, it should always be loaded using this file so that projects
 * using Composer continue to work.
 *
 * @see composer.json
 * @see index.php
 * @see core/install.php
 * @see core/rebuild.php
 * @see core/modules/statistics/statistics.php
 */

if (is_link(getcwd() . '/core')) {
  $drupal_root = getcwd();
}
else {
  $drupal_root = __DIR__;
}
return require $drupal_root . '/core/vendor/autoload.php';
