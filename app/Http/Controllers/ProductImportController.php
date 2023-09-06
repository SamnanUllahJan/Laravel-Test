<?php
  
namespace App\Http\Controllers;
  
use Illuminate\View\View;
use App\Jobs\ProductCSVData;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\Bus;
use App\Facades\ProductImporterFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
  
class ProductImportController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(): View
    {
        return view('productsImport');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function store(StoreRequest $request)
    {
        $file = $request->file('csv'); 
      
       $validation = ProductImporterFacade::importProductsFromCSV($file);
        if( $validation == null ){
            return redirect()->route('products.import.index')
            ->with('success', 'CSV Import added on queue.');
        }else{ 
            return redirect()->route('products.import.index')->withErrors($validation);
        }

            
         
    }
}