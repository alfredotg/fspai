<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Api;
use App\Folder;
use Illuminate\Support\Facades\DB;

class FolderTest extends TestCase
{
    use Api;

    public function testEmptyList()
    {
        $this->callApi('/folders', [])
            ->assertStatus(200)
            ->assertExactJson([]);
    }

    public function testSorted()
    {
        $folders = [
            Folder::create(['name' => 'b']),   
            Folder::create(['name' => 'a']),   
        ];

        $this->callApi('/folders', ['order' => 'desc'])
            ->assertStatus(200)
            ->assertJsonPath('0.name', 'b')
            ->assertJsonPath('1.name', 'a');

        $this->callApi('/folders', ['order' => 'asc'])
            ->assertStatus(200)
            ->assertJsonPath('0.name', 'a')
            ->assertJsonPath('1.name', 'b');

        $this->callApi('/folders', ['order' => 'Sasc'])
            ->assertStatus(400);
    }

    public function testLimit()
    {
        $folders = [
            Folder::create(['name' => 'b']),   
            Folder::create(['name' => 'a']),   
            Folder::create(['name' => 'c']),   
        ];

        $this->callApi('/folders', [])
            ->assertOk()
            ->assertJsonCount(3);

        $this->callApi('/folders', ['limit' => 2])
            ->assertOk()
            ->assertJsonCount(2);

        $this->callApi('/folders', ['limit' => 501])
            ->assertStatus(400);

        $this->callApi('/folders', ['limit' => '40.5'])
            ->assertStatus(400);
    }

    public function testAfterId()
    {   
        $folders = [
            Folder::create(['name' => 'b']),   
            Folder::create(['name' => 'b']),   
            Folder::create(['name' => 'a']),   
            Folder::create(['name' => 'c']),   
        ];

        $this->callApi('/folders', ['after_id' => 0])->assertJsonCount(4);
        $this->callApi('/folders', ['after_id' => $folders[0]->id])->assertJsonCount(2); // second b and c
        $this->callApi('/folders', ['after_id' => $folders[3]->id])->assertJsonCount(0);
    }
}
