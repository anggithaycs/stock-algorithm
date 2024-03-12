<?php

namespace App\Libraries;

use Config\Database;
use Config\GCS;
use Config\Vapid;

use Google\Cloud\Storage\StorageClient;

class ALLlibrary
{
    public function tsl_one($id, $again = 21)
    {
        echo $id . "-";
        $db = Database::connect();

        // No changes are needed for the tsl_check logic
        if ($again > 21)
            $db->query("UPDATE stocks SET tsl_check = ? WHERE id = ?", ['final', $id]);
        else
            $db->query("UPDATE stocks SET tsl_check = ? WHERE id = ?", ['yes', $id]);

        // Fetch the initial_tsl for the given id from TSLCalculationView
        $tslQuery = $db->query("SELECT initial_tsl FROM TSLCalculationView WHERE stock_id = ?", [$id]);
        $tslResult = $tslQuery->getRow();

        if (!$tslResult || $tslResult->initial_tsl === null) {
            echo "Initial TSL not found for stock\n";
            return;
        }

        // Update the stocks.tsl with the fetched initial_tsl value
        $db->query("UPDATE stocks SET tsl = ? WHERE id = ?", [$tslResult->initial_tsl, $id]);
        echo "Updated TSL: " . $tslResult->initial_tsl . " for stock ID " . $id . "\n";
    }
}
