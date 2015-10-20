<?php

require_once 'autoloader.php';

use BattleshipGame\Controllers\GameController;

$gameController = new GameController;
$gameController->start();
