<?php
include "functions.php";
$db = new PDO("mysql:host=localhost:3306;dbname=dbmusic", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);