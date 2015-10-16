<?php

namespace BattleshipGame\Entities

class Destroyer extends Ship
{
	public function __construct()
	{
		parent::__construct();
		$this->setName('Destroyer');
		$this->setSize(5);
	}

}
