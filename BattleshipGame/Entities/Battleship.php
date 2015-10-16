<?php
namespace BattleshipGame\Entities

class Battleship extends Ship
{
	public function __construct()
	{
		parent::__construct();
		$this->setName('Battleship');
		$this->setSize(5);
	}

}
