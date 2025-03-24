<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once('db.php');

if ( empty($_SESSION['id']) ) {
    $_SESSION['id'] = time();
}
const START_HOUR = 5;
const CHANNEL_PARAM = 'channel';
const FILE_PARAM = 'file';
const FILES_DIR = 'files';

spl_autoload_register();