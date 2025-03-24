<?php
	namespace Parser\Models;
	use PDO;
	use \Parser\Model;
	
	class EpgChannelModel extends Model
	{
        const TABLE_NAME = 'channels_epg';
		
		public function getByChnId( $channelId, $xmlData = [] )
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
		public function getArrByChnId( $channelId )
		{
			return $this->findMany("SELECT * FROM " . self::TABLE_NAME . " WHERE channel_id = :channel_id ORDER BY prior", ['channel_id' => $channelId]);
		}


		public function getByEpgId( $epgId, $sourceId, $xmlData = [] )
		{
			$db = $this->findClassOne("SELECT * FROM " . self::TABLE_NAME . " WHERE epg_id = :epg_id AND source_id = :source_id", '\\Parser\\EpgChannel', ['epg_id' => $epgId, 'source_id' => $sourceId]);
            if ($db) {
                $channelEpg = $db;
            } else {
                $channelEpg = new \Parser\EpgChannel;
            }
            foreach ($xmlData as $key => $value) {
                $channelEpg->$key = $value;
            }
            return $channelEpg;
        }

		public function getBySourceIdPairs($key, $value, $sourceId)
		{
			return $this->findMany("SELECT `$key`, `$value` FROM " . self::TABLE_NAME . " WHERE source_id = :source_id", ['source_id' => $sourceId], PDO::FETCH_KEY_PAIR);
		}

		public function getAll()
		{
			return $this->findMany("SELECT * FROM " . self::TABLE_NAME . " ");
		}
	}