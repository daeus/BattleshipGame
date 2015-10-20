<?php
/**
 * Battlegame field object
 *
 * @author Daeus
 */

namespace BattleshipGame\Entities;

use BattleshipGame\Config\FieldTerm;
use BattleshipGame\Config\Game;

class Field
{
	protected $_ships = [];
	protected $_hits = [];
	protected $_miss = [];
	protected $_fieldMatrix;
	protected $_colLabel;
	protected $_rowLabel;
	
	public function __construct()
	{
		$this->setColLabel(Game::COL_SIZE);
		$this->setRowLabel(Game::ROW_SIZE);
	}

	public static function loadField($ships, $hits, $miss)
	{
		$instance = new self;

		foreach($ships as $ship) $instance->addExistingShip($ship);
		$instance->setHits($hits);
		$instance->setMiss($miss);
		return $instance;
	
	}

	public static function createNewField()
	{
		$instance = new self;
		return $instance;
	}

	/**
	 * @param int $colsize
	 * @return void
	 */
	private function setColLabel($colSize)
	{
		$number = range(1, $colSize);
		foreach($number as $key => $val)
		{
			$lastDigit = $val % 10;
			$number[$key] = $lastDigit;
		}

		$this->_colLabel = $number;

	}

	/**
	 * @return array
	 */
	public function getColLabel()
	{
		return $this->_colLabel;	
	}
	
	/**
	 * @return array
	 */
	public function getRowLabel()
	{
		return $this->_rowLabel;	
	}

	/**
	 * @param int $rowSize
	 * @return void
	 */
	private function setRowLabel($rowSize)
	{
		unset($this->_rowLabel);

		$alphabetArray = range('A', 'Z');
		$this->_rowLabel = range($alphabetArray[0], $alphabetArray[$rowSize - 1]);
	}
	
	/**
	 * @param object $ship
	 * @return void
	 */
	public function addExistingShip($ship)
	{
		$this->_ships[] = $ship; 
	}

	/**
	 * @param object $ship
	 * @return void
	 */
	public function addNewShip($size)
	{
		if($this->_ships)
		{
			$isPlaced = false;
			$count = 0; 
			
			// count to prevent infinite loop
			while(!$isPlaced && $count < 50)
			{
				$ship = Ship::createNewShip($size, Game::ROW_SIZE, Game::COL_SIZE);

				// check if the ship overlapped
				$isOverlapped = false;
				foreach($this->_ships as $placedShip)
				{
					if($ship->isOverlap($placedShip))
					{
						$isOverlapped = true;
						break;
					}
				}

				if(!$isOverlapped)
				{
					$this->_ships[] = $ship;
					$isPlaced = true;
				}

				$count++;
			}
		
		} else {
			$ship = Ship::createNewShip($size, Game::ROW_SIZE, Game::COL_SIZE);
			$this->_ships[] = $ship; 
		}

		unset($ship);
	}

	/**
	 * @return object
	 */
	public function getShips()
	{
		return $this->_ships;
	}

	/**
	 * @return array
	 */
	public function getShipCoordinates()
	{
		$shipCoordinates = [];

		foreach($this->_ships as $ship)
		{
			$shipCoordinates[] = $ship->getCoordinates();
		}

		return $shipCoordinates;
	}

	/**
	 * @param array $miss
	 * @return void
	 */
	public function setMiss($miss)
	{
		$this->_miss = $miss;
	}

	/**
	 * @return array
	 */
	public function getMiss()
	{
		return $this->_miss;
	}

	/**
	 * @param array $hits
	 * @return void
	 */
	public function setHits($hits)
	{
		$this->_hits = $hits;
	}

	/**
	 * @return array
	 */
	public function getHits()
	{
		return $this->_hits;
	}

	/**
	 * @return void
	 */
	private function _draw()
	{
		$this->_fieldMatrix = array_fill(1, Game::ROW_SIZE, array_fill(1, Game::COL_SIZE, FieldTerm::NO_SHOT));
		if(!empty($this->_miss)){
			foreach($this->_miss as $miss)
			{
				$this->_fieldMatrix[$miss[0]][$miss[1]] = FieldTerm::MISS_SHOT;
			}
		}

		if(!empty($this->_hit))
		{
			foreach($this->_hits as $hit)
			{
				$this->_fieldMatrix[$hit[0]][$hit[1]] = FieldTerm::HIT_SHOT;
			}
		}
	}

	/**
	 * @return array
	 */
	public function getFieldMatrix()
	{
		$this->_draw();
		return $this->_fieldMatrix;
	}

	/**
	 * @todo
	 * @return array
	 */
	public function getShipMatrix()
	{
	
	}

	/**
	 * check if the hit is already used
	 * @param $hit
	 * @return boolean
	 */
	public function isFired($hit)
	{
		if($this->_hits)
		{
			foreach($this->_hits as $thisHit)
			{
				if($thisHit == $hit) return true;
			}
		}

		if($this->_miss)
		{
			foreach($this->_miss as $thisMiss)
			{
				if($thisMiss == $hit) return true;
			}
		}

		return false;
	
	}

	/**
	 * @param $hit 
	 * @return boolean
	 */
	public function checkHit($hit)
	{
		foreach($this->_ships as $ship)
		{
			if($ship->isHit($hit)) return true;
		}

		return false;
	}
}
