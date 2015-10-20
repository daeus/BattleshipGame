<?php

namespace BattleshipGame\Config;

class Lang{

	const ENTER_TEXT = 'Enter coordinates (row, col), e.g. A5 = ';
    const USER_INPUT_FORM = '<form method="post">%s<input type="text" name="command" /><input type="submit" /></form>';
	const INVALID_INPUT = 'Invalid Input. Please try again. ';
	const FIRED_INPUT = 'You have already fired the area. Please try other area. ';
	const MISS = 'Miss';
	const SUNK = 'Sunk! ';
	const HIT = 'Hit! ';
	const END_OF_GAME = 'Well done! You completed the game in %s shots. ';

}
