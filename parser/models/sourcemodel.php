<?php
	namespace Parser\Models;
	use \Parser\Model;
	
	class SourceModel extends Model
	{

        /**
         * Получает ИД источника ХМЛ-файла по его url
         * 
         * @param string $url url, указанный в ХМЛ-файле
         * 
         * @return string ИД в БД
         */
        public function getSourceId( string $url): string {
            return $this->findColumn("SELECT id FROM sources WHERE url = :url LIMIT 1", [ 'url' => $url, ]);
        }
    }