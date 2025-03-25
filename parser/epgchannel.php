<?php
namespace Parser;
/**
 * Канал в ХМЛ-файле
 */
class EpgChannel {
    private $id = 0;
    private $source_id = '';
    private $channel_id = 0;
    private $epg_id = '';
    private $epg_names = '';
    private $tz_offset = '+0300';
    private $tz_offset_wt = '+0300';
    private $prior = '';
    private $exl_test = 0;
    private $xmlEpgId = '';
    private $xmlEpgNames = '';
    private $xmlSourceId = '';

    public function __get($property)
    {
        return $this->$property;
    }
    public function __set($property, $value)
    {
        if ( !empty($value) ) {
            $this->$property = $value;
        }
    }
}