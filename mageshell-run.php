<?php

function usage()
{
    global $argv;
    echo "USAGE: {$argv[0]} [-u <username|email> -p<password>]\n";
    exit;
}

$options = getopt('u:p:a');

if (isset($options['h'])) {
    usage();
}

$argc = count($argv);

if (!in_array($argc, array(1, 2, 4))) {
    usage();
}

if (version_compare(phpversion(), '5.2.0', '<')) {
    echo 'Magento, and therefore Mageshell, requires PHP version >= 5.2.0';
    exit;
}

/**
 * Add the parent directory to the include path
 */
$magePath = dirname(dirname(__FILE__));
set_include_path(get_include_path() . PATH_SEPARATOR . $magePath);

/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);

/**
 * Load optional local.json config file
 */
$localConfigFilename = $magePath . '/app/etc/local.json';
if (file_exists($localConfigFilename)) {
    $env = json_decode(file_get_contents($localConfigFilename), true);
    foreach ($env as $key => $value) {
        putenv($key . '=' . $value);
    }
}

/**
 * Compilation includes configuration file
 */
$compilerConfig = $magePath . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = $magePath . '/app/Mage.php';

if (!file_exists($mageFilename)) {
    echo $mageFilename." was not found";
    exit;
}

require_once $mageFilename;

/**
 * Load the mageshell class file
 */
$mageShellFilename = 'Mageshell.php';
if (!file_exists($mageShellFilename)) {
    echo $mageShellFilename . 'was not found';
    exit;
}

require_once $mageShellFilename;

#Varien_Profiler::enable();

Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(022);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

Mage::init($mageRunCode, $mageRunType);

/* Start session */
if (isset($options['u']) && isset($options['p'])) {
    $username = $options['u'];
    $password = $options['p'];

    $user = Mage::getSingleton('admin/session')->login($username, $password);

    if (!is_object($user) || !$user->getId()) {
        echo "Admin login failed.\n";
    } else {
        echo "Logged in as {$user->getUsername()}\n";
    }
} else if (isset($options['a'])) {
    Mage::app()->setCurrentStore('admin');
    echo "Using admin context.\n";
} else {
    echo "No admin login.\n";
}

$mageshell = new Mageshell();
$mageshell->start();

