<?php
/**
 *
 * @author Daeus
 *
 */
namespace BattleshipGame\Controllers;

use BattleshipGame\Entities\Field;
use BattleshipGame\Entities\Ship;
use BattleshipGame\Config\Lang;
use BattleshipGame\Config\Game;

class GameController
{

	const TEMPLATE_FILE = 'BattleshipGame/Templates/game_field.html';
	const TEMPLATE_STATUS_TAG = '[%STATUS%]';
	const TEMPLATE_FIELD_TAG = '[%FIELD%]';
	const TEMPLATE_ENTER_TAG = '[%ENTER%]';
	protected $_field; // object
	protected $_fieldMatrix;
	protected $_gameData;
	protected $_ships;
	protected $_miss;
	protected $_hits;
	protected $_userInput;
	protected $_inputCoordinates;
	protected $_outputStatus;
	protected $_outputEnter;
	protected $_gameEnded = false;

	public function __construct()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', '1'); 

		$this->_outputStatus = '';
		$this->_outputEnter = sprintf(Lang::USER_INPUT_FORM, Lang::ENTER_TEXT);

		$this->_userInput = isset($_POST['command'])? strtoupper($_POST['command']) : '';
	}

	/**
	 * 
	 */
	public function start()
	{
		if($this->_userInput === 'RESTART'){
			$this->deleteSavedGame();		
		}

		if(isset($_COOKIE[Game::COOKIE_KEY]) && $this->_userInput === 'SHOW')
		{
			$this->loadGame();

		} elseif(isset($_COOKIE[Game::COOKIE_KEY])){
			$this->loadGame();	
			$this->_processInput();
		} else {
			$this->startNewGame();
		}

		$this->_renderHTMLTemplate();

		if($this->_gameEnded)
			deleteSavedGame();
	}

	/**
	 * Load Saved Game
	 * @return void
	 */
	public function loadGame()
	{
		$this->_gameData = json_decode($_COOKIE[Game::COOKIE_KEY], true);

		$ship = $this->_gameData['ships'];
		$miss = $this->_gameData['miss'];
		$hits = $this->_gameData['hits'];

		$this->_field = Field::loadField($ship, $hits, $miss);
	}

	/**
	 * @todo
	 * Start new Game
	 */
	public function startNewGame()
	{
		// Initialise a field
		$this->_field = Field::createNewField();

		// Place ships according to setting
		for($i = 1; $i <= 5; $i++)
		{
			for($j = 0; $j < Game::$NO_OF_SHIP[$i]; $j++)	
			{
				$this->_field->addNewShip($i);
			}
			
		}

		// save game data
		$this->saveGame();

	}

	/**
	 * Delete Saved Game
	 * return void
	 */
	public function deleteSavedGame()
	{
		if (isset($_COOKIE[Game::COOKIE_KEY])) {
			unset($_COOKIE[Game::COOKIE_KEY]);
			setcookie(Game::COOKIE_KEY, '', time() - 3600, '/'); // empty value and old timestamp
		}
	}

	/**
	 * Save game
	 * return void
	 */
	public function saveGame()
	{
		$gameData['ships'] = $this->_field->getShipCoordinates();
		$gameData['miss'] = $this->_field->getMiss();
		$gameData['hits'] = $this->_field->getHits();

		setcookie(Game::COOKIE_KEY, json_encode($gameData));
	}

	/**
	 * @todo phase 2: change the regex according to game setting
	 *
	 * @param array $input
	 * @return array
	 */
	private function _validateInput($input)
	{
		return preg_match('/[A-J](10|[0-9])/', $input);
	}

	/**
	 * @todo
	 */
	private function _processInput()
	{
		// Validate input
		if(!$this->_userInput)
		{
			return;
		} 
		elseif(!$this->_validateInput($this->_userInput))
		{
			$this->_outputStatus = Lang::INVALID_INPUT;
			return;
		}

		// translate input to be mechine readable coordinates
		$this->_inputCoordinates = $this->_translateInput($this->_userInput);

		// process input
		$hitShip = $this->_field->isHit($this->_inputCoordinates);
		if($hitShip)
		{
			$this->_field->addHit($this->_inputCoordinates);

			// Won the game
			if(count($this->_field->getHits) === $this->_field->getShipsLength()) {
				$this->_gameEnded = true;
				$this->_outputEnter = sprintf(Lang::END_OF_GAME, $this->_field->countTotalHit()); 

			// Sunk any ship
			} elseif($hitShip->isSunk($this->_field->getHits())){
				$this->_outputStatus = sprintf(Lang::SUNK, $hitShip->getName);

			// Just hit
			} else {
				$this->_outputStatus = sprintf(Lang::HIT, $hitShip->getName);
			
			}
		} else {
			$this->_field->addMiss($this->_inputCoordinates);
			$this->_outputStatus = Lang::MISS;
		}

		$this->saveGame();

	}

	/**
	 * @param string $input
	 * @return array
	 */
	private function _translateInput($input)
	{
		$input_array = str_split($input);
		$input_coord[0] = ord(strtoupper($input_array[0])) - ord('A') + 1;
		$input_coord[1] = (isset($input_array[2]))? $input_array[1] . $input_array[2]: $input_array[1];
		$input_coord[1] = ($input_coord[1] == 0)? 10:$input_coord[1];

		return $input_coord;
	}

	/**
	 * @todo addCheatMode
	 * @return string
	 */
	private function _fieldToHTML($cheatMode = false)
	{
		$matrix = $this->_field->getFieldMatrix();
		$colLabel = $this->_field->getColLabel();
		$rowLabel = $this->_field->getRowLabel();

		$html = '&nbsp;';
		foreach($colLabel as $elemColLabel)
			$html .= $elemColLabel;

		$html .= '<br />';
		for($i = 1; $i <= Game::ROW_SIZE; $i++)
		{
			$html .= $rowLabel[$i - 1];

			for($j = 1; $j <= Game::COL_SIZE; $j++)
				$html .= $matrix[$i][$j];

			$html .= '<br />';
		} 

		return $html;

	}

	/**
	 * @return string
	 */
	private function _renderHTMLTemplate()
	{
		$html = file_get_contents(self::TEMPLATE_FILE);
		$html = str_replace(self::TEMPLATE_STATUS_TAG, $this->_outputStatus, $html);
		$html = str_replace(self::TEMPLATE_FIELD_TAG, $this->_fieldToHTML(), $html);
		$html = str_replace(self::TEMPLATE_ENTER_TAG, $this->_outputEnter, $html);

		echo $html;
	}
}
