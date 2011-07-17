<?php

/**
 * Copyright (C) 2010-2011 Shadez <https://github.com/Shadez>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **/

// Start sessions
session_start();

// Display all errors
error_reporting(E_ALL);

// Detect main directory
define('WOW_DIRECTORY', dirname(dirname(__FILE__)));

// And check it
if(!defined('WOW_DIRECTORY') || !WOW_DIRECTORY) {
    die('<strong>Fatal Error</strong>: unable to detect directory for system files!');
}

define('DS', DIRECTORY_SEPARATOR);
define('INCLUDES_DIR',    WOW_DIRECTORY . DS . 'includes' . DS);
define('CLASSES_DIR',     INCLUDES_DIR . 'classes' . DS);
define('CONFIGS_DIR',     INCLUDES_DIR . 'configs' . DS);
define('INTERFACES_DIR',  INCLUDES_DIR . 'interfaces' . DS);
define('LOCALES_DIR',     INCLUDES_DIR . 'locales' . DS);
define('TEMPLATES_DIR',   INCLUDES_DIR . 'templates' . DS);
define('CONTROLLERS_DIR', INCLUDES_DIR . 'controllers' . DS);
define('WEBROOT_DIR',     WOW_DIRECTORY . DS . 'webroot' . DS);

// Load defines
include(INCLUDES_DIR . 'revision_nr.php');
include(INCLUDES_DIR . 'UpdateFields.php');
include(INCLUDES_DIR . 'SharedDefines.php');

// Load Interfaces
include(INTERFACES_DIR . 'interface.db.php');
include(INTERFACES_DIR . 'interface.log.php');

// Load config files
include(CONFIGS_DIR . 'DatabaseConfig.php');
include(CONFIGS_DIR . 'WoWConfig.php');

// Load libraries
include(CLASSES_DIR . 'libs' . DS . 'mysqldatabase.php');
include(CLASSES_DIR . 'libs' . DS . 'log.php');
// Load classes
include(CLASSES_DIR . 'class.db.php');
include(CLASSES_DIR . 'class.wow.php');
include(CLASSES_DIR . 'class.locale.php');
include(CLASSES_DIR . 'class.template.php');

include(CLASSES_DIR . 'class.pagecontroller.php');
include(CONTROLLERS_DIR . 'controller.php');

// Load data
include(INCLUDES_DIR . 'data' . DS . 'data.classes.php');
include(INCLUDES_DIR . 'data' . DS . 'data.races.php');

// Custom classes
// Register autoload method
spl_autoload_register('WoW_Autoload');

/**
 * Autoload classes. Do not call this function manually, it will be called automatically.
 * 
 * @category WoW Loader
 * @access   global / public
 * @param    string $className
 * @return   void
 **/
function WoW_Autoload($className)
{
    //prevent error when mailer classes are loaded
    if(!preg_match('/swift_(.*)/i', $className)) {
        $className = strtolower(str_replace('WoW_', null, $className));
        if(!file_exists(CLASSES_DIR . 'class.' . $className . '.php')) {
            die('<strong>Autoload Fatal Error:</strong> unable to autoload class ' . $className . '!');
        }
        include(CLASSES_DIR . 'class.' . $className . '.php');
    }
}
// Initialize debug log
WoW_Log::Initialize(WoWConfig::$UseLog, WoWConfig::$LogLevel);

// Load databases configs
// Do not create DB connections here - it will be created automatically at first query request!
DB::LoadConfigs();

// Core self testing
WoW::SelfTests();

// Try to catch some operations (login, logout, etc.)
$locale_loaded = false;
WoW::CatchOperations($locale_loaded);

// locale from page controller
$wow_locale_cms = (isset($_COOKIE['wow_locale']) ? $_COOKIE['wow_locale'] : WoWConfig::$DefaultLocale);
if(!isset($_COOKIE['wow_locale'])) {
    setcookie('wow_locale', $wow_locale_cms, strtotime('NEXT YEAR'), '/');
    $_COOKIE['wow_locale'] = $wow_locale_cms;
}
$pController = new PageController();
if($pController->GetLocale() != null && $pController->GetLocale() != $_COOKIE['wow_locale']) {
    //$_SESSION['wow_locale'] = $pController->GetLocale();
    WoW_Locale::SetLocale($pController->GetLocale(), WoW_Locale::GetLocaleIDForLocale($pController->GetLocale()), true);
}
else {
    WoW_Locale::SetLocale($_COOKIE['wow_locale'], WoW_Locale::GetLocaleIDForLocale($_COOKIE['wow_locale']));
}

// Initialize account (if user already logged in we need to re-build his info from session data)
WoW_Account::Initialize();

if(isset($_COOKIE['wow_session']) && !WoW_Account::IsLoggedIn()) {
    WoW_Account::loadFromCookieSession();
}

// Initialize auction handler
WoW_Auction::InitAuction(); // TODO: this initialization should be moved in some other place.

// Display wow revision (if required; do not remove).
if(isset($_GET['_DISPLAYVERSION_'])) {
    die('WOW_REVISION: ' . WOW_REVISION);
}

// RunOnce
define('__RUNONCE__', true);
include(INCLUDES_DIR . 'RunOnce.php');
?>
