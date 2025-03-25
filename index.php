<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
use Parser\View;
use Parser\FS;
use Parser\EpgXml;
$fs = new FS;
$xml = new EpgXml;
if ( ! empty($_FILES) ) {
    $upload = $fs->uploadFile();
    if ($upload) {
        header('Location: /?mode=process_xml');
    }
    die();
}
if ( ! empty($_GET['del']) && ! empty($_GET[FILE_PARAM]) ) {
    if ( $fs->delete($_GET[FILE_PARAM]) ) {
        header('Location: /');
    }
    die();
}
if ( isset($_REQUEST['startpos']) && isset($_REQUEST['fileindex']) ) {
    $index = intval($_REQUEST['fileindex']);
    $tree = $fs->getLoadFilesInfo();
    if ( ! empty($tree[$index]) ) {
        $path = $tree[$index]['path'];
        $data['startpos'] = $xml->processXml($path, $_REQUEST['startpos']);
        $data['files'] = $tree;
    } else {
        $data['startpos'] = false;
        $data['files'] = false;
    }
    echo json_encode($data);
    die();
}
if ( $_SERVER['QUERY_STRING'] == 'mode=archive_cats' ) {
    $dir = $fs->getCatsDir();
    $fs->writePrograms($dir, 'cat_id');
    $zipPath = $fs->createZip($dir);
    $fs->loadZip($zipPath);
    die();
}
if ( $_SERVER['QUERY_STRING'] == 'mode=archive_channels' ) {
    $dir = $fs->getChannelsDir();
    $fs->writePrograms($dir, 'name');
    $zipPath = $fs->createZip($dir);
    $fs->loadZip($zipPath);
    die();
}
new View();
