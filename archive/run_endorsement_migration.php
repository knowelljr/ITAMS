<?php

try {
    $connection = new PDO('sqlsrv:server=localhost,1433;database=itams;TrustServerCertificate=yes', 'sa', 'afh@1234');
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents('database/migrations/013_add_endorsement_fields_to_issuances.sql');
    
    $connection->exec($sql);
    
    echo "✓ Migration completed successfully!" . PHP_EOL;
    echo "✓ Endorsement fields added to asset_issuances table" . PHP_EOL;
    
    // Verify the columns were added
    $stmt = $connection->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'asset_issuances' 
        AND COLUMN_NAME IN ('endorsement_type', 'endorsed_employee_number', 'endorsement_remarks')
    ");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo PHP_EOL . "Verified columns:" . PHP_EOL;
    foreach ($columns as $col) {
        echo "  ✓ $col" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
