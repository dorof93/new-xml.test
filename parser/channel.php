<?php
namespace Parser;
class Channel {
    private $xmlChannelId = '';
    private $id = 0;
    private $name = '';
    private $cat_id = 0;
    private $note = '';
    private $utc_tz = '+0300';
    private $utc_tz_wt = '+0300';
    private $sort = 99;
    private $epg = [];

    public function __get($property)
    {
        return $this->$property;
    }
    public function __set($property, $value)
    {
        $properties = [
            'epg',
        ]; // неразрешенные свойства
        if ( ! empty($value) && ! in_array($property, $properties) ) {
            $this->$property = $value;
        }
    }
    public function setEpg($value) {
        if ( is_array($value) ) {
            $this->epg = $value;
        }
    }
}