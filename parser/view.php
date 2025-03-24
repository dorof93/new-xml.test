<?php
namespace Parser;
use Parser\EpgXml;
use Parser\Channel;
use Parser\FS;
use Parser\Models\ChannelModel;
use Parser\Models\CatModel;
use Parser\Models\ProgModel;

class View {
    private $tree = [];
    private $workspaceMode = '';
    private $title = '';
    private $h1 = '';
    private $description = '';

    public function __construct() {
        if ( ! empty($_GET['channel']) ) {
            //
        } elseif ( $_SERVER['QUERY_STRING'] == 'mode=db_channels' ) {
            $this->title = 'Управление каналами';
            $this->h1 = 'Управление каналами';
            $this->workspaceMode = $_GET['mode'];
        } elseif ( $_SERVER['QUERY_STRING'] == 'mode=new_channels' ) {
            $this->title = 'Новые каналы в EPG';
            $this->h1 = 'Новые каналы в EPG';
            $this->workspaceMode = $_GET['mode'];
        } elseif ( $_SERVER['QUERY_STRING'] == 'mode=process_xml' ) {
            $this->title = 'Генерация программы';
            $this->h1 = 'Генерация программы';
            $this->description = 'Генерация программы из XML в человекопонятный вид';
            $this->workspaceMode = $_GET['mode'];
        } else {
            $this->title = 'XML-EPG-парсер - загрузить файлы';
            $this->description = 'Конвертер EPG в текстовую телепрограмму';
            $this->h1 = 'Загрузка XML-файла';
        }
        require('views/layout.php');
    }
    private function workspace() {
        if ( ! empty($_GET['channel']) ) {
            $channels[] = (new ChannelModel)->getById($_GET['channel']);
            require('views/editChannels.php');
            return true;
        }
        switch ($this->workspaceMode) {
            case 'db_channels':
                $channels[] = new Channel;
                require('views/editChannels.php');
                break;
            case 'new_channels':
                $channels = (new EpgXml)->getNewChannels();
                require('views/editChannels.php');
                break;
            case 'process_xml':
                $generate = (new ProgModel)->isExistGenId();
                require('views/processXml.php');
                break;
            default:
                require('views/loadXml.php');
                break;
        }
        return true;
    }
    private function showTree($param) {
        switch($param) {
            case CHANNEL_PARAM:
                $this->tree = (new ChannelModel)->getAllPairs('id', 'name');
                break;
            case FILE_PARAM:
                $this->tree = (new FS)->getLoadFiles();
                break;
        }
        require('views/tree.php');
    }
    private function notices() {
        require('views/notices.php');
    }
    private function submitForm() {
        require('views/submit.php');
    }
    private function channelsForm($channels) {
        $cats = (new CatModel)->getAll();
        foreach ($channels as $key => $row) { 
            require('views/channelForm.php');
        }
    }
}