<?php
	namespace Parser\Models;
	use PDO;
	use \Parser\Model;
	use \Parser\Models\EpgChannelModel;
	use \Parser\Channel;
	
	class ChannelModel extends Model
	{
		/**
		 * Получает канал из БД по ИД
		 * 
		 * @param int $id ИД канала
		 * @param array $xmlData данные канала из XML-файла (подставляются в соответствующий ЕПГ-канал)
		 * 
		 * @return Channel объект класса Channel
		 */
		public function getById( int $id, array $xmlData = [] ): Channel
		{
			$db = $this->findClassOne("SELECT * FROM channels WHERE id = :id", '\\Parser\\Channel', ['id' => $id]);
            if ($db) {
                $channel = $db;
            } else {
                $channel = new Channel;
            }
            $epgChannel = new EpgChannelModel;
            $channel->setEpg($epgChannel->getByChnId( $id, $xmlData ));
            return $channel;
		}
		
		// public function getByCatId( $catId )
		// {
		// 	return $this->findMany("SELECT * FROM channels WHERE cat_id = :cat_id ORDER BY name", ['cat_id' => $catId]);
		// }

		/**
		 * Получает массив всех каналов в формате 1-й столбец - ключ, 2-й столбец - значение
		 * 
		 * @param string $key столбец для ключа
		 * @param string $value столбец для значения
		 * 
		 * @return array
		 */
		public function getAllPairs( string $key, string $value): array
		{
			return $this->findMany("SELECT `$key`, `$value` FROM channels ORDER BY name", [], PDO::FETCH_KEY_PAIR);
		}

		/**
		 * Получает массив всех каналов
		 * 
		 * @return array
		 */
		public function getAll(): array
		{
			return $this->findMany("SELECT * FROM channels ORDER BY name");
		}
	}