<?php
namespace App\Traits;

use App\Models\Categories;
use Illuminate\Support\Facades\DB;

trait CategoryTrait{

    function SelectCategoryList(){
        $categories = Categories::where('is_active','1')->whereNull('deleted_at')->select('id','name')->get();
        return $categories;
    }


}
