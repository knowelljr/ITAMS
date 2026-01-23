<?php
require 'vendor/autoload.php';
use App\Database\Connection;

$db = Connection::getInstance()->getConnection();
$result = $db->query('SELECT TOP 1 * FROM inventory_stores');
$row = $result->fetch(\PDO::FETCH_ASSOC);
if ($row) {
  echo 'Store columns: ' . implode(', ', array_keys($row)) . "\n";
  echo "Store data:\n";
  foreach ($row as $k => $v) {
    echo "  $k = $v\n";
  }
}
?>
