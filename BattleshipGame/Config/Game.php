<?php

namespace BattleshipGame\Config;

class Game{

	const COOKIE_KEY = 'BattleshipGame';
	const SESSION_KEY = 'BattleshipGame';
	const ROW_SIZE = '10'; // Max 10
	const COL_SIZE = '10'; // Max 10
	public static $SHIP_NAME = array(
		1 => 'small ship',
		2 => 'medium ship',
		3 => 'large ship',
		4 => 'destroyer',
		5 => 'battleship'
	);

	public static $NO_OF_SHIP = array(
		1 => 0, 
		2 => 0, 
		3 => 0, 
		4 => 2, 
		5 => 1, 
	);

}

