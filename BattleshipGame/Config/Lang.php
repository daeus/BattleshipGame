<?php

namespace BattleshipGame\Config;

class Lang{

	const ENTER_TEXT = 'Enter coordinates (row, col), e.g. A5 = ';
    const USER_INPUT_FORM = '<form method="post">%s<input type="text" name="command" /><input type="submit" /></form>';
	const INVALID_INPUT = 'Invalid Input. Please try again. ';
	const MISS = 'Miss';
	const SUNK = '%s is sunk! ';
	const HIT = 'Hit a %s! ';
	const END_OF_GAME = 'Well done! You completed the game in %s shots. ';

}
