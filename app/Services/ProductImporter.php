<?php

namespace App\Services;

use App\Models\Product;
use App\Events\ProductEvent;
use App\Jobs\ProductCSVData;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductImporter
{
    public function importProductsFromCSV($file)
    {
        // Saving the file to storage for reading it as CSV
        // Otherwise, it will break even faster.
        $file = $file->store('csv', ['disk' => 'public']);

        // Opening the file for reading
        $fileStream = fopen(storage_path('app/public/' . $file), 'r');

        $csvContents = [];
      
        // Reading the file line by line into an array
        while (($line = fgetcsv($fileStream)) !== false) {

            $csvContents[] = $line;
            
        }

        // Closing the file stream
        fclose($fileStream);

        $skipHeader = true;
        $batch  = Bus::batch([])->dispatch();
        
        // Attempt to import the CSV
        foreach ($csvContents as $content) {

            if ($skipHeader) {
                // Skipping the header column (first row)
                $skipHeader = false;
                continue;
            }

            $fieldMap = [
                'title',
                'description',
                'sku',
                'type',
                'cost_price',
                'status',
            ];

            // Replace empty values with null in the $content array
            $content = array_map(function ($value) {
                return empty($value) ? null : $value;
            }, $content);
         
            $Input = array_combine($fieldMap, $content);
           
            $validator = Validator::make($Input, [
                'title' => 'string|required',
                'description' => 'string|required',
                'sku' => 'string|required',
                'type' => 'string|required',
                'cost_price' => 'numeric|required',
                'status' => 'string|required',
            ]);
        
            // Check validation failure
            if ($validator->passes() == true) {
            
                $val = null; 
                $batch->add(new ProductCSVData($Input, Auth::user()));

            }else { 
               
                $val = $validator;
                break;

            }
         
        }
       
        // Deleting the file from storage (it was temporary!)
        Storage::delete($file);
       
        return $val;
    }
}
