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

	const TEMPLATE_FILE = 'Battleship/Templates/game_field.html';
	const TEMPLATE_STATUS_TAG = '[%STATUS%]';
	const TEMPLATE_FIELD_TAG = '[%FIELD%]';
	const TEMPLATE_ENTER_TAG = '[%ENTER%]';
	protected $_field; // object
	protected $_gameData;
	protected $_ships;
	protected $_miss;
	protected $_hits;
	protected $_userInput;
	protected $_inputCoordinates;
	protected $_outputStatus;
	protected $_outputEnter;

	public function __construct()
	{
		$this->_outputStatus = '';
		$this->_outputEnter = sprintf(Lang::USER_INPUT_FORM, Lang::ENTER_TEXT);
	}

	public static function getInstance()
	{
		if(isset($_COOKIE[Game::COOKIE_KEY]))
		{
			$this->loadGame();	
			$this->_processInput();
			$this->_renderHTMLTemplate();
		} else {
			$this->startNewGame();
			$this->_renderHTMLTemplate();
		}
	}

	/**
	 * Load Saved Game
	 * return void
	 */
	public function loadGame()
	{
		$this->_gameData = json_decode($_COOKIE[Game::COOKIE_KEY]);
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
			for($j = 0; $j < Game::NO_OF_SHIP[$i]; $j++)	
				$this->_field->addNewShip();
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
	 * @todo
	 * @param array $coordinate
	 * @return array
	 */
	private function _validateInput($coordinate)
	{
		if($coordinate[0])
		{
		
		}
		return true;
	}

	/**
	 * @todo
	 */
	private function _processInput()
	{
		$this->_userInput = isset($_POST['command']) ? $_POST['command'] : '';

		if($this->_validateInput($this->_userInput))
		{
			$this->_outputStatus = Lang::INVALID_INPUT;
			return;
		}

		$this->_inputCoordinates = $this->_translateInput($this->_userInput);
		$this->
	}

	/**
	 * @todo 
	 */
	private function _translateInput()
	{
	
	}

	/**
	 * @todo
	 */
	private function _fieldToHTML()
	{

		$matrix = $this->_field->getFieldMatrix();
		$colLabel = $this->_field->getColLable();
		$rowLabel = $this->_field->getRowLable();

		$html = '&nbsp;';
		foreach($colLabel as $elemColLabel)
			$html .= $elemColLabel;

		$html .= '<br />';
		for($i = 1; $i <= Game::ROW_SIZE; $i++)
		{
			$html .= $rowLabel[];
			for($j = 1; $j <= Game::COL_SIZE; $j++)
			{
			
			} 
		} 

		foreach($matrix as $row)
		{
			foreach($row as $elem)
				$html .= $elem;

			$html .= '<br />';
		}

	}

	/**
	 * @todo
	 */
	private function _renderHTMLTemplate()
	{
		$html = file_get_contents(self::TEMPLATE_FILE);
		$html = str_replace(self::TEMPLATE_STATUS_TAG, $this->$_outputStatus);
		$html = str_replace(self::TEMPLATE_FIELD_TAG, $this->$_field->);
	}
}
