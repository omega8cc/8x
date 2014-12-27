<?php

/**
 * @file
 * Rebuilds all Drupal caches even when Drupal itself does not work.
 *
 * Needs a token query argument which can be calculated using the
 * scripts/rebuild_token_calculator.sh script.
 *
 * @see drupal_rebuild()
 */

use Drupal\Component\Utility\Crypt;
use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Change the directory to the Drupal root.
chdir(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));

$autoloader = require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/utility.inc';

$request = Request::createFromGlobals();
// Manually resemble early bootstrap of DrupalKernel::boot().
require_once __DIR__ . '/includes/bootstrap.inc';
DrupalKernel::bootEnvironment();

try {
  Settings::initialize(dirname(__DIR__), DrupalKernel::findSitePath($request), $autoloader);
}
catch (HttpExceptionInterface $e) {
  $response = new Response('', $e->getStatusCode());
  $response->prepare($request)->send();
  exit;
}

if (Settings::get('rebuild_access', FALSE) ||
  ($request->get('token') && $request->get('timestamp') &&
    ((REQUEST_TIME - $request->get('timestamp')) < 300) &&
    ($request->get('token') === Crypt::hmacBase64($request->get('timestamp'), Settings::get('hash_salt')))
  )) {

  drupal_rebuild($autoloader, $request);
  drupal_set_message('Cache rebuild complete.');
}
$base_path = dirname(dirname($request->getBaseUrl()));
header('Location: ' . $base_path);
