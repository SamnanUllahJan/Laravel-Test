<?php
  
namespace App\Jobs;
  
use App\Models\User;
use App\Models\Product;
use App\Events\ProductEvent;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
  
class ProductCSVData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
 
    public $data;

    public $user;
  
    /**
     * Create a new job instance.
     */
    public function __construct( $data , $user)
    {
        $this->data = $data; 

        $this->user = $user; 
    }
  
    /**
     * Execute the job.
     */
    public function handle(): void
    {    
      try {
        Product::create($this->data);
       
        $count = Product::where('sku',$this->data['sku'])->count();
  
        if($count > 0){
         
        //Send notification to user
        event(new ProductEvent($this->data , $this->user ));
        }

        // Log success
        Log::info('ProductCSVData job processed successfully.');
    } catch (\Exception $e) {
        // Log error
        Log::error('ProductCSVData job failed: '.Auth::user()->id.' Start Error' . $e->getMessage());
    }
 


    }
}