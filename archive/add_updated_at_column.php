<?php

try {
    $connection = new PDO('sqlsrv:server=localhost,1433;database=itams;TrustServerCertificate=yes', 'sa', 'afh@1234');
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding updated_at column to asset_issuances table..." . PHP_EOL;
    
    $sql = "
        IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'asset_issuances' AND COLUMN_NAME = 'updated_at')
        BEGIN
            ALTER TABLE asset_issuances
            ADD updated_at DATETIME DEFAULT GETDATE();
            
            PRINT 'updated_at column added successfully';
        END
        ELSE
        BEGIN
            PRINT 'updated_at column already exists';
        END;
    ";
    
    $connection->exec($sql);
    
    echo "✓ Migration completed successfully!" . PHP_EOL;
    echo "✓ updated_at column added to asset_issuances table" . PHP_EOL;
    
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
