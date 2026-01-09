<?php

class PersonalizationModel extends Mysql{

	public function __construct()
	{
		parent::__construct();
	}

	public function getTema(){
				
		$query = "SELECT * FROM personalization WHERE active = 1 LIMIT 1";
		
		$request = $this -> select_all($query);
		
		return $request;
	}

    public function saveTema($id, $color1, $color2, $color3, $color4, $img1, $img2, $img3){
        $this->id = $id;
		$this->color1 = $color1;
        $this->color2 = $color2;
        $this->color3 = $color3;
        $this->color4 = $color4;
        $this->img1 = $img1;
        $this->img2 = $img2;
        $this->img3 = $img3;
        $sql = "UPDATE personalization SET color1 = ?, color2 = ?, color3 = ?, color4 = ?, img1 = ?, img2 = ?, img3 = ? WHERE id = ?";
		$request = $this->update($sql, [$this->color1, $this->color2, $this->color3, $this->color4, $this->img1, $this->img2, $this->img3, $this->id]);

		return $request;
    }

}
?>