<?php
namespace Parser;
use \Parser\Models\ChannelModel;
use \Parser\Models\EpgChannelModel;
use \Parser\Models\ProgModel;
use ZipArchive;
/**
 * Функции для работы с файловой системой
 */
class FS {
    /**
     * Получает путь к рабочей папке для текущей сессии пользователя
     * 
     * @return string
     */
    public function getDir(): string {
        $filesDir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . FILES_DIR;
        if ( ! file_exists($filesDir) ) {
            mkdir($filesDir, 0755, true);
        }
        return $filesDir . DIRECTORY_SEPARATOR . $_SESSION['id'] . DIRECTORY_SEPARATOR;
    }
    /**
     * Получает путь к папке для хранения программы по категориям каналов
     * 
     * @return string
     */
    public function getCatsDir(): string {
        return $this->getDir() . 'cats' . DIRECTORY_SEPARATOR;
    }
    /**
     * Получает путь к папке для хранения программы по каналам
     * 
     * @return string
     */
    public function getChannelsDir (): string {
        return $this->getDir() . 'channels' . DIRECTORY_SEPARATOR;
    }
    /**
     * Получает массив загруженных ХМЛ-файлов 
     * 
     * @return array
     */
    public function getLoadFiles(): array {
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
    /**
     * Получает массив загруженных ХМЛ-файлов с информацией о них
     * 
     * @return array
     */
    public function getLoadFilesInfo(): array {
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
    /**
     * Загружает ХМЛ-файл
     * 
     * @return bool
     */
    public function uploadFile(): bool
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
    /**
     * Удаляет ХМЛ-файл
     * 
     * @param string $fileName название файла
     * 
     * @return bool
     */
    public function delete( string $fileName ): bool {
        $dir = $this->getDir();
        if ( file_exists($dir . $fileName) ) {
            return unlink($dir . $fileName);
        }
        return false;
    }


    /**
     * Читает программу из БД и записывает в текстовые файлы
     * 
     * @param string $dir папка для записи файлов
     * @param string $groupField по какому полю из БД группировать
     * 
     * @return bool
     */
    public function writePrograms( string $dir, string $groupField ): bool {
        if ( ! file_exists($dir) ) {
            mkdir($dir, 0755, true);
        } else {
            return false; // сделать нормальную проверку, что программа уже сгенерирована
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
        return true;
    }

    /**
     * Архивирует папку в Zip
     * 
     * @param string $dir путь к папке
     * 
     * @return string
     */
    public function createZip( string $dir ): string {
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

    /**
     * Отдает зип-архив в браузер 
     * 
     * @param string $zipPath путь к зип-архиву
     * 
     * @return void
     */
    public function loadZip( string $zipPath ): void {
        if ( file_exists($zipPath) ) {
            $zipName = basename($zipPath);
            header('Content-Type: application/zip, application/octet-stream, application/x-zip-compressed, multipart/x-zip');
            header('Content-Disposition: attachment; filename=' . $zipName);
            echo file_get_contents($zipPath);
        }
    }
}
