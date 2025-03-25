<?php
	namespace Parser\Models;
	use PDO;
    use DateTimeImmutable;
    use DateTimeZone;
    use SimpleXMLElement;
	use \Parser\Model;
    use \Parser\Models\EpgChannelModel;
	
	class ProgModel extends Model
	{
        const TABLE_NAME = 'progs';

        /**
         * Проверяет, была ли программа уже занесена в БД в текущей сессии пользователя
         * 
         * @return bool
         */
        public function isExistGenId(): bool {

            $genId = $_SESSION['id'];
			$count = $this->findColumn(
                "SELECT COUNT(*) FROM " . self::TABLE_NAME . " WHERE gen_id = :gen_id", 
                ['gen_id' => $genId,]
            );
            return $count > 0;

        }

        /**
         * Получает программу для ЕПГ-канала
         * 
         * @param string $epgChnId ИД ЕПГ-канала в ХМЛ-файле
         * 
         * @return array
         */
        public function getByEpgChnId( string $epgChnId ): array {

            $genId = $_SESSION['id'];
			return $this->findMany(
                "SELECT * FROM " . self::TABLE_NAME . " WHERE channels_epg_id = :channels_epg_id AND gen_id = :gen_id ORDER BY id", 
                ['channels_epg_id' => $epgChnId, 'gen_id' => $genId,]
            );

        }


        /**
         * Читает ХМЛ-файл и записывает программу в БД
         * 
         * @param SimpleXMLElement $xml ХМЛ-объект
         * @param int $sourceId ИД источника XML-файла
         * 
         * @return void
         */
        public function insertFromXml ( SimpleXMLElement $xml, int $sourceId ): void {
            $progs = $xml->programme;
            $pdo = parent::$pdo;
            $genId = $_SESSION['id'];
            $channelsTz = (new EpgChannelModel)->getBySourceIdPairs('epg_id', 'tz_offset', $sourceId);
            $stmt = $pdo->prepare("INSERT INTO progs (`channels_epg_id`, `date`, `text`, `desc`, `type`, `gen_id`, `source_id`) VALUES (:channels_epg_id, :date, :text, :desc, :type, :gen_id, :source_id)");
            try {
                $pdo->beginTransaction();
                foreach ($progs as $prog) {
                    $start        = (string) $prog['start'];
                    $stop         = (string) $prog['stop'];
                    $channelEpgId = (string) $prog['channel'];
                    $title        = (string) $prog->{'title'};
                    $desc         = (string) $prog->{'desc'};
                    $type         = (string) $prog->{'category'};
                    $tz           = $channelsTz[$channelEpgId]; // из БД
                    
                    
                    $startFullTime = $this->getTzOffsetTime($start, $tz);
                    $startDateOffset = $this->getDateOffsetStartHour($startFullTime);
                    $startTime = substr($startFullTime, -5);
                    
                    $insert = [
                        'channels_epg_id' => $channelEpgId,
                        'date' => $startDateOffset,
                        'text' => $startTime . ' ' . $title,
                        'desc' => $desc,
                        'type' => $type,
                        'gen_id' => $genId,
                        'source_id' => $sourceId,
                    ];
                    $stmt->execute($insert);
                }
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollback();
                throw $e;
            }
        }
        
        /**
         * Переводит время в соответствующий часовой пояс
         * 
         * @param string $strTime строка с датой и временем
         * @param string $tz обозначение часового пояса
         * 
         * @return string время для часового пояса
         */
        private function getTzOffsetTime ( string $strTime, string $tz ): string {
            $d = new DateTimeImmutable($strTime);
            $tzo = new DateTimeZone($tz);
            $local = $d->setTimezone($tzo);
            return $local->format('Y-m-d H:i');
        }
    
        /**
         * Устанавливает, с какого часа начинать сутки
         * 
         * @param string $strTime строка с датой и временем
         * 
         * @return string дата
         */
        private function getDateOffsetStartHour ( string $strTime ): string {
            $hourSec = START_HOUR * 60 * 60;
            $date = date('Y-m-d', strtotime($strTime) - $hourSec);
            return $date;
        }
    }