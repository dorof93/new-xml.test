<?php
	namespace Parser\Models;
	use \Parser\Model;
	
	class SourceModel extends Model
	{

        public function getSourceId($url) {
            return $this->findColumn("SELECT id FROM sources WHERE url = :url LIMIT 1", [ 'url' => $url, ]);
        }
    }