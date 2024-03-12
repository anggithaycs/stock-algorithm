<?php

namespace App\Commands;

use Config\Database;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Config\BaseConfig;

class TSL extends BaseCommand
{
    protected $group       = '';
    protected $name        = '';
    protected $description = 'Set initial TSL stocks';

    public function run(array $params)
    {
        $db = Database::connect();
        $x = new \App\Libraries\ALLlibrary();

        // Step 1: Get all distinct id, barcode, and product_id where product_id is not null and tsl is null
        $query = $db->query("SELECT id FROM stocks WHERE product_id IS NOT NULL AND tsl IS NULL AND tsl_check='no' ORDER BY id DESC limit 250");
        $results1 = $query->getResult();

        // Loop through all the results and apply the processing logic to each
        if (!empty($results1)) {
            foreach ($results1 as $result) {
                $x->tsl_one($result->id);
            }
        }

        // Step 1: Get all distinct id, barcode, and product_id where product_id is not null and tsl is null
        $query = $db->query("SELECT id FROM stocks WHERE product_id IS NOT NULL AND tsl IS NULL AND tsl_check='yes' ORDER BY id DESC limit 250");
        $results2 = $query->getResult();
        // Loop through all the results and apply the processing logic to each
        if (!empty($results2)) {
            foreach ($results2 as $result) {
                $x->tsl_one($result->id, 60);
            }
        }

        // No longer use the price condition as it's handled in the SQL view and keep the tsl_check update
        $query = $db->query("SELECT id FROM stocks WHERE product_id IS NOT NULL AND tsl_check='final'");
        $results4 = $query->getResult();
        if (!empty($results4)) {
            foreach ($results4 as $result) {
                echo $result->id . "-";
                $db->query("UPDATE stocks SET tsl_check = ? WHERE id = ?", ['end', $result->id]);
                // Removed the price condition logic
                echo "Updated tsl_check to 'end' for stock ID " . $result->id . "\n";
            }
        }
    }
}
