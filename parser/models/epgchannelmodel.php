<?php
	namespace Parser\Models;
	use PDO;
	use \Parser\Model;
	use \Parser\EpgChannel;
	
	class EpgChannelModel extends Model
	{
        const TABLE_NAME = 'channels_epg';
		
		/**
         * Получает ЕПГ-каналы по ИД канала в массив объектов класса EpgChannel
         * 
		 * @param int $channelId ИД канала
		 * @param array $xmlData Данные из ХМЛ-файла
		 * 
		 * @return array
		 */
		public function getByChnId( int $channelId, array $xmlData = [] ): array
		{
            $channelsEpg = [];
			$db = $this->findClassMany("SELECT * FROM " . self::TABLE_NAME . " WHERE channel_id = :channel_id ORDER BY prior", '\\Parser\\EpgChannel', ['channel_id' => $channelId]);
            if ( is_array($db) )  {
                foreach ($db as $obj) {
                    $xmlEpgId = $xmlData['xmlEpgId'] ?? '';
                    $xmlSourceId = $xmlData['xmlSourceId'] ?? '';
                    if ( $obj->epg_id == $xmlEpgId && $obj->source_id == $xmlSourceId ) {
                        foreach ($xmlData as $key => $value) {
                            $obj->$key = $value;
                        }
                    }
                    $channelsEpg[] = $obj;
                }
            }
            return $channelsEpg;
		}
		/**
         * Получает ЕПГ-каналы по ИД канала в обычный массив
         * 
		 * @param int $channelId ИД канала
		 * 
		 * @return array
		 */
		public function getArrByChnId( int $channelId ): array
		{
			return $this->findMany("SELECT * FROM " . self::TABLE_NAME . " WHERE channel_id = :channel_id ORDER BY prior", ['channel_id' => $channelId]);
		}


		/**
         * Получает ЕПГ-канал из БД по ИД в XML-файле
         * 
		 * @param string $epgId ИД ЕПГ-канала в XML-файле
		 * @param int $sourceId ИД источника XML-файла
		 * @param array $xmlData данные ЕПГ-канала из ХМЛ-файла
		 * 
		 * @return EpgChannel объект класса EpgChannel
		 */
		public function getByEpgId( string $epgId, int $sourceId, array $xmlData = [] ): EpgChannel
		{
			$db = $this->findClassOne("SELECT * FROM " . self::TABLE_NAME . " WHERE epg_id = :epg_id AND source_id = :source_id", '\\Parser\\EpgChannel', ['epg_id' => $epgId, 'source_id' => $sourceId]);
            if ($db) {
                $channelEpg = $db;
            } else {
                $channelEpg = new EpgChannel;
            }
            foreach ($xmlData as $key => $value) {
                $channelEpg->$key = $value;
            }
            return $channelEpg;
        }

		/**
         * Получает массив всех каналов в формате 1-й столбец - ключ, 2-й столбец - значение для конкретного источника XML-файла
         * 
		 * @param string $key столбец для ключа
		 * @param string $value столбец для значения
		 * @param int $sourceId ИД источника XML-файла
		 * 
		 * @return array
		 */
		public function getBySourceIdPairs( string $key, string $value, int $sourceId ): array
		{
			return $this->findMany("SELECT `$key`, `$value` FROM " . self::TABLE_NAME . " WHERE source_id = :source_id", ['source_id' => $sourceId], PDO::FETCH_KEY_PAIR);
		}

		/**
         * Получает все ЕПГ-каналы
         * 
		 * @return array
		 */
		public function getAll(): array
		{
			return $this->findMany("SELECT * FROM " . self::TABLE_NAME . " ");
		}
	}