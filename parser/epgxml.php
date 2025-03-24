<?php
namespace Parser;
use PDO;
use \Parser\Models\ChannelModel;
use \Parser\Models\EpgChannelModel;
use \Parser\Models\SourceModel;
use \Parser\Models\ProgModel;
use \Parser\FS;

class EpgXml {
    public function getNewChannels () {
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

    public function processXml ($filePath, $startPos = 0) {
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

    public function getSourceUrl($filePath) {
        $xml = file_get_contents($filePath, FALSE, NULL, 0, 1000);
        if ( $xml ) {
            preg_match('#generator-info-url="(.*?)"#', $xml, $matches);
            if ( ! empty($matches[1]) ) {
                return $matches[1];
            }
        }
        return '';
    }
    public function getSourceId($filePath) {
        $url = $this->getSourceUrl($filePath);
        $id = (new SourceModel)->getSourceId($url);
        return $id;
    }
    private function getDisplayNames($obj) {
        $display_names_arr  = array();
        foreach ($obj as $display_name) {
            $display_names_arr[] = $display_name;
        }
        $display_names = implode(',', $display_names_arr);
        return $display_names;
    }
}