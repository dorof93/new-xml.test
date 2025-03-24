<?php
	namespace Parser\Models;
	use PDO;
	use \Parser\Model;
	use Parser\Models\EpgChannelModel;
	
	class ChannelModel extends Model
	{
		public function getById( $id, $xmlData = [] )
		{
			$db = $this->findClassOne("SELECT * FROM channels WHERE id = :id", '\\Parser\\Channel', ['id' => $id]);
            if ($db) {
                $channel = $db;
            } else {
                $channel = new \Parser\Channel;
            }
            $epgChannel = new EpgChannelModel;
            $channel->setEpg($epgChannel->getByChnId( $id, $xmlData ));
            return $channel;
		}
		
		// public function getByCatId( $catId )
		// {
		// 	return $this->findMany("SELECT * FROM channels WHERE cat_id = :cat_id ORDER BY name", ['cat_id' => $catId]);
		// }

		public function getAllPairs($key, $value)
		{
			return $this->findMany("SELECT `$key`, `$value` FROM channels ORDER BY name", [], PDO::FETCH_KEY_PAIR);
		}

		public function getAll()
		{
			return $this->findMany("SELECT * FROM channels ORDER BY name");
		}
	}