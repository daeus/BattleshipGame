<?php
/**
 * This is a basic component for a ship
 * @author Daeus
 */

namespace BattleshipGame\Entities;

use BattleshipGame\Config\Game;

class Ship
{
	protected $_name;
	protected $_size;
	protected $_coordinates = array();

	public function __construct()
	{
	}

	/**
	 * load saved ship
	 * @param array $coordinates
	 * @return object
	 */
	public static function loadShip($coordinates)
	{
		$instance = new self();
		$instance->setSize(count($coordinates));
		$instance->setName(Game::$SHIP_NAME[$instance->getSize()]);
		$instance->setCoordinates($coordinates);
		return $instance;
	}

	/**
	 * Create New Ship
	 * @param int $size
	 * @param int $fieldRow
	 * @param int $fieldCol
	 */
	public static function createNewShip($shipSize, $fieldRow, $fieldCol)
	{
		$instance = new self();
		$instance->setName(Game::$SHIP_NAME[$shipSize]);
		$instance->setSize($shipSize);
		$instance->setCoordinates($instance->randomPlaceShip($shipSize, $fieldRow, $fieldCol));
		
		return $instance;
	}

	/**
	 * @param int $shipSize
	 * @param int $fieldRow
	 * @param int $fieldCol
	 * @return array
	 */
	public function randomPlaceShip($shipSize, $fieldRow, $fieldCol)
	{
		// Direction of ship
		$isHorizontal = rand(0, 1);

		if($isHorizontal)
		{
			$startPoint[0] = rand(1, $fieldRow);
			$startPoint[1] = rand(1, $fieldCol - $shipSize);
		} else {
			$startPoint[0] = rand(1, $fieldRow - $shipSize);
			$startPoint[1] = rand(1, $fieldCol);
		}

		// set first point
		$coordinates = array();

		for($i = 0; $i < $shipSize; $i++)
		{
			if($isHorizontal)
			{
				$coordinates[] = array($startPoint[0], $startPoint[1] + $i); 
			} else {
				$coordinates[] = array($startPoint[0] + $i, $startPoint[1]); 
			
			}
		}

		return $coordinates;
	
	}


	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name) 
	{
		$this->_name = $name;
	}

	/**
	 * @param int $size
	 * @return void
	 */
	public function setSize($size)
	{
		if(is_int($size) && $size > 0)
		{
			$this->_size = $size;
		} else {
			throw \Exception('Size of the ship is invalid. ');
		}
	}

	/**
	 * @param int $size
	 * @return void
	 */
	public function getSize($size)
	{
		return $this->_size;
	}

	/**
	 * @param array $coordinates
	 * @return void
	 */
	public function setCoordinates($coordinates)
	{
		$this->_coordinates = $coordinates;
	}

	/**
	 * @return array
	 */
	public function getCoordinates()
	{
		return $this->_coordinates;
	}

	/**
	 * @param Ship $placedShip
	 * @return boolean
	 */
	public function isOverlap($placedShip)
	{
		foreach($placedShip->getCoordinates() as $otherCoordinates)		
		{
			foreach($this->getCoordinates() as $thisCoordinates)	
			{
				if($otherCoordinates === $thisCoordinates) return true;	
			}
		}
		return false;
	
	}

	/**
	 * @param array $hits 
	 * @return boolean
	 */
	public function isSunk($hits)
	{
		$countHit = 0;
		foreach($this->getCoordinates() as $thisCoordinates)
		{
			foreach($hits as $hit)
			{
				if($hit === $thisCoordinates) $countHit++;
			}
		}

		return ($countHit == $this->getSize())?true:false;
	}

	/**
	 * @param array $hit
	 * @return boolean
	 */
	public function isHit($hit)
	{
		foreach($this->getCoordinates() as $thisCoordinates)
			if($hit == $thisCoordinates) return true;

		return false;
	}

}
