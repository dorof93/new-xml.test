<?php
namespace Parser;
use PDO;
use SimpleXMLElement;
use \Parser\Models\ChannelModel;
use \Parser\Models\EpgChannelModel;
use \Parser\Models\SourceModel;
use \Parser\Models\ProgModel;
use \Parser\FS;

/**
 * Функции для работы с ХМЛ-файлами
 */
class EpgXml {
    /**
     * Читает ХМЛ-файлы и получает каналы, которых еще нет в БД
     * 
     * @return array массив объектов класса Channel
     */
    public function getNewChannels (): array {
        $newChannels = [];
        $files = (new FS)->getLoadFilesInfo();
        foreach ($files as $file) {
            $filePath = $file['path'];
            $xml = file_get_contents($filePath, FALSE, NULL, 0, 1000000);
            if ( ! $xml ) {
                return false;
            }
            $endXml = '</tv>';
            $endTag = '</channel>';
            $endTagLen = strlen($endTag);
            $lastTagPos = strrpos($xml, $endTag);
            $endPos = $lastTagPos + $endTagLen;
            $xml = substr($xml, 0, $endPos) . $endXml;

            $xmlLoad = simplexml_load_string($xml);
            $xmlSourceId = $this->getSourceId($filePath);
            $channels = $xmlLoad->channel;

            foreach ($channels as $key => $channel) {
                $epgId = (string) $channel['id'];
                $displayNames = $this->getDisplayNames($channel->{'display-name'});
                $xmlData = [
                    'xmlEpgId' => $epgId, 
                    'xmlEpgNames' => $displayNames,
                    'xmlSourceId' => $xmlSourceId,
                ];
                $epgChannel = (new EpgChannelModel)->getByEpgId($epgId, $xmlSourceId, $xmlData);
                
                $dbChnId = $epgChannel->channel_id;
                if ( empty($dbChnId) ) {
                    $channel = new Channel;
                    $channel->setEpg([$epgChannel]);
                    $newChannels[] = $channel;
                } elseif ($epgChannel->epg_names != $epgChannel->xmlEpgNames) {
                    $newChannels[] = (new ChannelModel)->getById($dbChnId, $xmlData);
                }
            }
        }
        return $newChannels;
    }

    /**
     * Читает ХМЛ-файл и формирует ХМЛ-объект
     * 
     * @param string $filePath путь к ХМЛ-файлу
     * @param int $startPos с какой позиции (в байтах) начинать чтение
     * 
     * @return int позиция, на которой было закончено чтение (либо false, если достигнут конец файла)
     */
    public function processXml ( string $filePath, int $startPos = 0): int|false {
        $res = false;
        $startXml = '<?xml version="1.0" encoding="utf-8" ?><tv>';
        $endXml = '</tv>';
        $progEndTag = '</programme>';
        $progEndTagLen = strlen($progEndTag);
        $cutLength = 1000000;
        $fileSize = filesize($filePath);
        
        $xml = file_get_contents($filePath, FALSE, NULL, $startPos, $cutLength);
        $lastTagPos = strrpos($xml, $progEndTag);
        $endPos = $lastTagPos + $progEndTagLen;
        $nextStartPos = $startPos + $endPos;
        $xml = substr($xml, 0, $endPos);
        if ( ! empty($startPos) ) {
            $xml = $startXml . $xml;
        }
        if ( $nextStartPos < $fileSize ) {
            $xml = $xml . $endXml;
            $res = $nextStartPos;
        }
        $sourceId = $this->getSourceId($filePath);
        $xmlLoad = simplexml_load_string($xml);
        (new ProgModel)->insertFromXml($xmlLoad, $sourceId);
        return $res;
    }

    /**
     * Получает Url источника из ХМЛ-файла
     * 
     * @param string $filePath путь к ХМЛ-файлу
     * 
     * @return string Url источника
     */
    public function getSourceUrl( string $filePath ): string {
        $xml = file_get_contents($filePath, FALSE, NULL, 0, 1000);
        if ( $xml ) {
            preg_match('#generator-info-url="(.*?)"#', $xml, $matches);
            if ( ! empty($matches[1]) ) {
                return $matches[1];
            }
        }
        return '';
    }
    /**
     * Получает ИД источника ХМЛ-файла в БД
     * 
     * @param string $filePath путь к ХМЛ-файлу
     * 
     * @return string
     */
    public function getSourceId( string $filePath): string {
        $url = $this->getSourceUrl($filePath);
        $id = (new SourceModel)->getSourceId($url);
        return $id;
    }
    /**
     * Получает названия каналов из ХМЛ-файла
     * 
     * @param SimpleXMLElement $obj хмл-объект с названиями
     * 
     * @return string
     */
    private function getDisplayNames( SimpleXMLElement $obj ): string {
        $display_names_arr  = array();
        foreach ($obj as $display_name) {
            $display_names_arr[] = $display_name;
        }
        $display_names = implode(',', $display_names_arr);
        return $display_names;
    }
}