<?php
	namespace Parser\Models;
	use \Parser\Model;
	
	class CatModel extends Model
	{
		public function getAll()
		{
			return $this->findMany("SELECT * FROM cats ORDER BY name");
		}
	}