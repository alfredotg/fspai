<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Api;
use App\Page;
use App\Folder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PageTest extends TestCase
{
    use Api;

    public function testEmptyList()
    {
        $this->callApi('/pages', [])
            ->assertStatus(200)
            ->assertExactJson([]);
    }

    public function testFilter()
    {
        $folder = Folder::create(['name' => 'home']);
        $pages = [
            $this->createPage('a', $folder),   
            $this->createPage('b', $folder),   
            $this->createPage('d', $folder),   
            $page_c = $this->createPage('c', $folder),   
        ];

        $this->callApi('/pages', ['order' => 'desc', 'limit' => 2, 'after_id' => $page_c->id])
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.name', 'b')
            ->assertJsonPath('1.name', 'a');

    }

    public function testCreate()
    {
        $this->callApi('/page/create')
            ->assertStatus(400);

        $this->callApi('/page/create', ['name' => "About"])
            ->assertStatus(400)
            ->assertJsonStructure(['error' => ['folder_id']]);

        $this->callApi('/page/create', ['name' => "About", 'folder_id' => 1000])
            ->assertStatus(400)
            ->assertJsonStructure(['error' => ['folder_id']]);

        $folder = Folder::create(['name' => 'home']);
        $r = $this->callApi('/page/create', ['name' => 'About', 'folder_id' => $folder->id])
            ->assertStatus(201)
            ->assertJsonStructure(['id']);
        $page = Page::findOrFail($r->getData()->id);

        $this->assertEquals('About', $page->name);
        $this->assertEquals($folder->id, $page->folder_id);
        $this->assertEquals($page->status, Page::STATUS_DRAFT);
    }

    public function testSave()
    {   
        $page = $this->createPage('My home page');        

        $this->callApi('/page/save')
            ->assertStatus(400);

        $this->callApi('/page/save', ['id' => $page->id, 'name' => "About", 'content' => Str::random(Page::CONTENT_MAX_SIZE + 1)])
            ->assertStatus(400)
            ->assertJsonStructure(['error' => ['content']]);

        $this->callApi('/page/save', ['id' => $page->id, 'name' => "About", 'content' => 'Page content'])
            ->assertStatus(200);

        $page = Page::findOrFail($page->id);
        $this->assertEquals($page->name, 'About');
        $this->assertEquals($page->content, 'Page content');
        $this->assertEquals($page->status, Page::STATUS_DRAFT);
    }

    public function testPublish()
    {
        Storage::fake('pages');

        $page = $this->createPage('My home page');        
        $page->content = Str::random(200);
        $page->status = Page::STATUS_PUBLISHED;
        $page->save();

        $this->callApi('/page/publish')
            ->assertStatus(400);

        $res = $this->callApi('/page/publish', ['id' => $page->id])
            ->assertStatus(400)
            ->assertJsonStructure(['error' => ['status']]);

        $page->status = Page::STATUS_DRAFT;
        $page->save();

        $res = $this->callApi('/page/publish', ['id' => $page->id])
            ->assertStatus(201);

    }

    public function createPage(string $name, Folder $folder = null): Page
    {
        if($folder == null)
            $folder = Folder::create(['name' => 'home']);
        $page = new Page([
            'name' => $name,
            'content' => '',
            'status' => Page::STATUS_PUBLISHED
        ]);
        $page->folder_id = $folder->id;
        $page->save();
        return $page;
    }
}
