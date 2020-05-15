<?php

namespace Tests;

trait Api
{
    function callApi(string $path, array $data = [])
    {
        return $this->postJson('/api/v0'.$path, $data);
    }
}
