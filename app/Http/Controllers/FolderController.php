<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Folder;
use App\Http\Controllers\Traits\ApiModelList;

class FolderController extends Controller
{
    use ApiModelList;

    /*
     * /folders list of folders
     *
     * @param string order (asc, desc)
     * @param int limit Maximum number of entries (in range 1-500)
     * @param int after_id Filter entries with ID greater than given ID
     *
     * @return []Folder
     *
     */
    public function index(Request $request) 
    {
        return $this->modelList($request, Folder::query());
    }
}
