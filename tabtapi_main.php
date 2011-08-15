<?php
/****************************************************************************
 * TabT API
 *  A programming interface to access information managed
 *  by TabT, the table tennis information manager.
 * -----------------------------------------------------------------
 * Main API entry point
 * -----------------------------------------------------------------
 * @version 0.7.7
 * -----------------------------------------------------------------
 * Copyright (C) 2007-2011 Gaëtan Frenoy (gaetan@frenoy.net)
 * -----------------------------------------------------------------
 * This file is part of TabT API
 *
 * TabT API is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TabT API.  If not, see <http://www.gnu.org/licenses/>.
 **************************************************************************/

////////////////////////////////////////////////////////////////////////////

// Load main configuration file
if (!@include_once('config.inc')) {
  print('TabT not correctly installed on server.  Configuration file is missing.');
  exit;
}

// TabT includes
if (!@include_once($GLOBALS['site_info']['path'].'phplib/prepend.php'))
{
  print('TabT not correctly installed on server.');
  exit;
}

// Some constants 
define('WSDL_FILENAME', 'tabt.wsdl');
define('TABTAPI_VERSION', '0.7.7');

// disabling WSDL cache (for test servers only)
ini_set("soap.wsdl_cache_enabled", "0");

if (!isset($GLOBALS['site_info']['api_url']) || '' == $GLOBALS['site_info']['api_url']) {
  $GLOBALS['site_info']['api_url'] .= 'http'.($_SERVER['HTTPS']?'s':'')."://{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']}?s={$GLOBALS['site_info']['database']}";
}

// User requests WSDL
if (isset($_GET['WSDL']) || isset($_GET['wsdl'])) {
  header('Content-Type: text/xml');
  $content = str_replace('#VERSION#', TABTAPI_VERSION, file_get_contents(WSDL_FILENAME));
  $content = str_replace('#DATE#', date('Y-m-d', filemtime(WSDL_FILENAME)), $content);
  $content = str_replace('#URL#', $GLOBALS['site_info']['api_url'] , $content);
  $content = str_replace('#DATABASE#', $GLOBALS['site_info']['database'], $content);
  print($content);
  exit;
}
// User request documentation
if (isset($_GET['DOC']) || isset($_GET['doc'])) {
  header('Location: tabtapi-doc/index.html');
  exit;
}

// Specifiy default language
$lang = 'nl';

// Some general helper functions
if (!@include_once($GLOBALS['site_info']['path'].'public/helpers.php'))
{
  print('TabT not correctly installed on server (helpers not found).');
  exit;
}
// i18n
if (!@include_once($GLOBALS['site_info']['path'].'public/localization.php'))
{
  print('TabT not correctly installed on server (i18n not found).');
  exit;
}

// TabT API Includes
if (!@include_once('tabtapi_helpers.php')) {
  print('TabT not correclty installed (error loading tabtapi_helpers)');
}
if (!include_once('tabtapi.php')) {
  print('TabT not correclty installed (error loading tabtapi_helpers)');
}

// Dispatch calls
$server = new SoapServer(WSDL_FILENAME);
$server->addFunction(SOAP_FUNCTIONS_ALL);
try
{
  $server->handle();
}
catch (Exception $e)
{
  throw new SoapFault('22', "Unexpected database error [{$e->getMessage()}].");
}

?>
