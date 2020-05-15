<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Page;
use App\Rules\FindModel;
use App\Folder;
use App\Http\Controllers\Traits\ApiModelList;

class PageController extends Controller
{
    use ApiModelList;

    /*
     * /pages list of pages
     *
     * @param string order (asc, desc)
     * @param int limit Maximum number of entries (in range 1-500)
     * @param int after_id Filter entries with ID greater than given ID
     *
     * @return []Page
     */
    public function index(Request $request) 
    {
        return $this->modelList($request, Page::query());
    }

    /*
     * /page/create Create new page
     *
     * @param string name 
     *
     * @return Page
     */
    public function create(Request $request) 
    {
        $find_folder = new FindModel([Folder::class, 'find']);

        $validator = Validator::make($request->all(), [
            'name' => sprintf('required|max:%d', Page::NAME_MAX_SIZE),
            'folder_id' => [
                'bail',
                'required',
                'integer',
                $find_folder,
            ],
        ]);

        if($validator->fails()) 
            return $this->badRequest($validator->errors());

        $page = new Page(['name' => $request->name]);
        $page->folder_id = $find_folder->model->id;
        $page->status = Page::STATUS_DRAFT;
        $page->save();
        return $page;
    }

    /*
     * /page/save Save page
     *
     * @param integer id 
     * @param string name 
     * @param string content
     *
     * @return Page
     */
    public function save(Request $request) 
    {
        $find_page = new FindModel([Page::class, 'find']);

        $max_content_rule = function ($attribute, $value, $fail) {
            if($value && strlen($value) > Page::CONTENT_MAX_SIZE)
                $fail('Content is too big');
        };

        $validator = Validator::make($request->all(), [
            'id' => [
                'bail',
                'required',
                'integer',
                $find_page
            ],
            'name' => sprintf('max:%d', Page::NAME_MAX_SIZE),
            'content' => $max_content_rule
        ]);

        if($validator->fails()) 
            return $this->badRequest($validator->errors());

        $page = $find_page->model;
        $page->status = Page::STATUS_DRAFT;
        if($request->name)
            $page->name = $request->name;
        if($request->content)
            $page->content = $request->content;
        $page->save();
        return $page;
    }

    /*
     * /page/save Publish page
     *
     * @param integer id 
     *
     * @return string file path
     */
    public function publish(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => [
                'bail',
                'required',
                'integer'
            ]
        ]);

        if($validator->fails()) 
            return $this->badRequest($validator->errors());
        
        $page_path = sprintf('%d.html', $request->id);
        $disk = Storage::disk('public');
        DB::transaction(function() use($disk, $request, $page_path) {
            $page = Page::where('id', '=', $request->id)->lockForUpdate()->first();
            if(!$page)
                return $this->badRequest(['id' => 'Page not found']);
            if($page->status != Page::STATUS_DRAFT)
                return $this->badRequest(['status' => 'This page has already been published.']);
            $page->status = Page::STATUS_PUBLISHED;
            $page->save();
            $disk->put($page_path, $page->content ?? '');
        });
        return response()->json($disk->url($page_path), 201);
    }
}
