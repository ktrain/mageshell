<?php

function usage()
{
    global $argv;
    echo "USAGE: {$argv[0]} [-u <username|email> -p<password>]\n";
    exit;
}

$options = getopt('u:p:');

if (isset($options['h'])) {
    usage();
}

$argc = count($argv);

if (!in_array($argc, array(1, 4))) {
    usage();
}

if (version_compare(phpversion(), '5.2.0', '<')) {
    echo 'Magento, and therefore Mageshell, requires PHP version >= 5.2.0';
    exit;
}

/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);

/**
 * Load optional local.json config file
 */
$env = json_decode(file_get_contents("app/etc/local.json"), true);
foreach ($env as $key => $value) {
    putenv($key . '=' . $value);
}

/**
 * Compilation includes configuration file
 */
$compilerConfig = 'includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = 'app/Mage.php';

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
} else {
    echo "No admin login.\n";
}

$mageshell = new Mageshell();
$mageshell->start();

