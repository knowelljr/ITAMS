<?php
require 'vendor/autoload.php';
use App\Database\Connection;

$db = Connection::getInstance()->getConnection();
$result = $db->query('SELECT TOP 1 * FROM assets');
$row = $result->fetch(\PDO::FETCH_ASSOC);
if ($row) {
  echo 'Asset columns: ' . implode(', ', array_keys($row)) . "\n";
}
?>
