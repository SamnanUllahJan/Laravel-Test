<?php

namespace App\Facades; 

use Illuminate\Support\Facades\Facade;

class ProductImporterFacade extends Facade
{
        protected static function getFacadeAccessor()
    {
        return 'product-importer'; // This should match the binding in Step 3
    }
}