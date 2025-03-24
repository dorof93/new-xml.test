<?php
namespace Parser;
use \Parser\Models\ChannelModel;
use \Parser\Models\EpgChannelModel;
use \Parser\Models\ProgModel;
use ZipArchive;
class FS {
    public function getDir () {
        $filesDir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . FILES_DIR;
        if ( ! file_exists($filesDir) ) {
            mkdir($filesDir, 0755, true);
        }
        return $filesDir . DIRECTORY_SEPARATOR . $_SESSION['id'] . DIRECTORY_SEPARATOR;
    }
    public function getCatsDir () {
        return $this->getDir() . 'cats' . DIRECTORY_SEPARATOR;
    }
    public function getChannelsDir () {
        return $this->getDir() . 'channels' . DIRECTORY_SEPARATOR;
    }
    public function getLoadFiles () {
        $items = [];
        $dir = $this->getDir();
        if ( file_exists($dir) ) {
            $files = scandir($dir);
            foreach ($files as $key => $file) {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if ( ! is_dir($path) ) {
                    $items[$file] = $file;
                }
            }
        }
        return $items;
    }
    public function getLoadFilesInfo () {
        $items = [];
        $dir = $this->getDir();
        if ( file_exists($dir) ) {
            $files = scandir($dir);
            foreach ($files as $key => $file) {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if ( ! is_dir($path) ) {
                    $items[] = [
                        'key'=> $key,
                        'name' => $file,
                        'path' => $path,
                        'size' => filesize($path),
                    ];
                }
            }
        }
        return $items;
    }
    public function uploadFile()
    {
        if ( ! empty($_FILES['xmlfile']['name']) ) {
            $uploaddir = $this->getDir();
            if ( ! file_exists($uploaddir) ) {
                mkdir($uploaddir, 0755, true);
            }
            $pathinfo = pathinfo($_FILES['xmlfile']['name']);
            $uploadfile_ext = strtolower($pathinfo['extension']);
            switch ($uploadfile_ext) {
                case 'xml':
                    $format = '.' . $uploadfile_ext;
                break;
                default:
                    $format = '';
                break;
            }
            if ( !empty($format) ) {
                $new_filename = time() . $format;
                $file_path = $uploaddir . $new_filename;
                if ( is_uploaded_file($_FILES['xmlfile']['tmp_name']) && move_uploaded_file($_FILES['xmlfile']['tmp_name'], $file_path) ) {
                    return true;
                }
            }
        }
        return false;
    }
    public function delete($fileName) {
        $dir = $this->getDir();
        if ( file_exists($dir . $fileName) ) {
            return unlink($dir . $fileName);
        }
        return false;
    }


    public function writePrograms($dir, $groupField) {
        if ( ! file_exists($dir) ) {
            mkdir($dir, 0755, true);
        } else {
            return; // сделать нормальную проверку, что программа уже сгенерирована
        }
        $progChnId = 0;
        $progDate = 0;
        $channels = (new ChannelModel)->getAll();
        foreach ($channels as $channel) {
            if ( ! isset($channel[$groupField]) ) {
                return false;
            }
            $epgChannels = (new EpgChannelModel)->getArrByChnId($channel['id']);
            foreach ($epgChannels as $epgChannel) {
                $progs = (new ProgModel)->getByEpgChnId($epgChannel['epg_id']);
                if ( ! empty($progs) ) {
                    foreach ($progs as $key => $prog) {
                        $content = '';
                        if ( $progChnId !== $prog['channels_epg_id'] || $progDate !== $prog['date'] ) {
                            $content .= "\n\n";
                            $content .= "[%%channel]{$channel['name']}[channel%%]\n";
                            $content .= $prog['date'] . "\n\n";
                            $content .= "Time zone: UTC {$channel['utc_tz']}\n\n";
                        }
                        $content .= $prog['text'];
                        if ( ! empty($prog['type']) && $prog['type'] != 'Детям' ) {
                            $content .= ' -- ' . $prog['type'];
                        }
                        $content .= "\n";
                        $filePath = $dir . $prog['date'] . '__' . $channel[$groupField] . '.txt';
                        file_put_contents($filePath, $content, FILE_APPEND);
                        $progChnId = $prog['channels_epg_id'];
                        $progDate = $prog['date'];
                    }
                    break;
                }
            }
        }
    }

    public function createZip($dir) {
        $zipPath = $dir . 'program.zip';
        if ( ! file_exists($zipPath) ) {
            $zip = new ZipArchive();
            $zip->open($zipPath, ZIPARCHIVE::CREATE);
            $files = scandir($dir);
            foreach ($files as $key => $file) {
                $path = $dir . $file;
                if ( ! is_dir($path) ) {
                    $zip->addFile($path, $file);
                }
            }
            $zip->close();
        }
        return $zipPath;
    }

    public function loadZip($zipPath) {
        if ( file_exists($zipPath) ) {
            $zipName = basename($zipPath);
            header('Content-Type: application/zip, application/octet-stream, application/x-zip-compressed, multipart/x-zip');
            header('Content-Disposition: attachment; filename=' . $zipName);
            echo file_get_contents($zipPath);
        }
    }
}