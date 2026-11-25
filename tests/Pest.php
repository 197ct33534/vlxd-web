<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests chạy trên Tests\TestCase (boot Laravel) + RefreshDatabase.
| phpunit.xml trỏ DB test vào SQLite :memory: nên mỗi lần chạy sẽ migrate
| một DB rỗng, không đụng database.sqlite thật.
|
*/

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations / Helpers
|--------------------------------------------------------------------------
*/
