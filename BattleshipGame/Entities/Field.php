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
	 * @param array $shipCoord
	 * @return void
	 */
	public function addExistingShip($shipCoord)
	{
		$this->_ships[] = Ship::loadShip($shipCoord); 
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
	 * @return int
	 */
	public function getShipsLength()
	{
		$len = 0;
		foreach($this->_ships as $ship)
		{
			$len += count($ship->getCoordinates());
		}
		return $len;
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
	 * @param array $miss
	 * @return void
	 */
	public function addMiss($hit)
	{
		$this->_miss[$hit[0].$hit[1]] = $hit;
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
	 * @param array $miss
	 * @return void
	 */
	public function addHit($hit)
	{
		$this->_hits[$hit[0].$hit[1]] = $hit;
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

		if(!empty($this->_hits))
		{
			foreach($this->_hits as $hit)
			{
				$this->_fieldMatrix[$hit[0]][$hit[1]] = FieldTerm::HIT_SHOT;
			}
		}

		return $this->_fieldMatrix;
	}

	/**
	 * @return array
	 */
	public function getFieldMatrix($showShip = false)
	{
		return ($showShip)?$this->_drawShip():$this->_draw();
	}

	/**
	 * @return array
	 */
	private function _drawShip()
	{
		$this->_fieldMatrix = array_fill(1, Game::ROW_SIZE, array_fill(1, Game::COL_SIZE, FieldTerm::EMPTY_FIELD));
		$ships = $this->getShipCoordinates();

		foreach($ships as $ship)
		{
			foreach($ship as $coord)
			{
				$this->_fieldMatrix[$coord[0]][$coord[1]] = FieldTerm::HIT_SHOT;
			}
		}
		
		return $this->_fieldMatrix;
	}

	/**
	 * @param $hit 
	 * @return ship | false
	 */
	public function isHit($hit)
	{
		foreach($this->_ships as $ship)
		{
			if($ship->isHit($hit)) return $ship;
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function countTotalHit()
	{
		return count($this->_hits) + count($this->_miss);
	}
}
