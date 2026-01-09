<?php
class SystemLanguagesModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}

	public function getSystemLanguages($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM system_languages  
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";
		
		$request = $this -> select_all($query);
		
		return $request;
	}

	public function getDictionary(){
		$sql = "SELECT id, word FROM system_languages_dictionary";
		$request = [];
		foreach($this->select_all($sql) as $w){
			$sql = "SELECT sln.translation, sl.color, sl.id FROM system_languages_definition sln INNER JOIN system_languages sl ON(sln.system_language_id=sl.id) WHERE sln.languages_dictionary_id = {$w['id']} AND sln.system_language_id IN (1,2)";
			array_push($request, [
				'id'			=> $w['id'],
				'word'			=> $w['word'],
				'translations'	=> $this->select_all($sql)
			]);
		}
		return $request;
	}
	
	public function getLanguages(){
		$sql = "SELECT sl.id, sl.name, sl.color, COUNT(sln.translation) AS 'count' FROM system_languages sl INNER JOIN system_languages_definition sln ON(sln.system_language_id=sl.id) WHERE sl.id IN (1,2) GROUP BY sl.id, sl.name, sl.color";
		$request = $this->select_all($sql);
		return $request;
	}

	public function genJsonLanguages(int $system_language_id){
		$sql = "SELECT sly.word, sln.translation FROM system_languages_dictionary sly INNER JOIN system_languages_definition sln ON(sly.id = sln.languages_dictionary_id) WHERE sln.system_language_id = $system_language_id";
        
        $request = array_reduce($this->select_all($sql), function($carry, $item){
            $carry[$item['word']] = $item['translation'];
            return $carry;
        }, []);

		return $request;
	}

	public function insTranslate(int $dictionary_id, int $language_id, string $word){
		$word = str_replace(["'", '"'], ['´', '´'], $word);
		$sql = "INSERT INTO system_languages_definition(system_language_id, languages_dictionary_id, translation) VALUES (?, ?, ?)";
		$request = $this->insert($sql, [$language_id, $dictionary_id, $word]);
		return $request > 0? 1 : 0;
	}

	public function removeTranslate(int $dictionary_id, int $language_id){
		$sql = "DELETE FROM system_languages_definition WHERE system_language_id = $language_id AND languages_dictionary_id = $dictionary_id";
		$request = $this->delete($sql);
		return $request? 1 : 0;
	}
}
?>