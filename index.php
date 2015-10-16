<?php

session_start();

require_once 'autoloader.php';

use BattleshipGame\Controllers\GameController;

$gameController = GameController::getInstance();
