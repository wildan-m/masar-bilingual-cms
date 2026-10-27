<?php

namespace Tests;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Pages are rendered without a compiled asset build.
        $this->withoutVite();
    }

    protected function contactPage(): Page
    {
        return Page::factory()->contact()->create();
    }

    protected function admin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function editor(): User
    {
        return User::factory()->editor()->create();
    }

    protected function intake(): User
    {
        return User::factory()->intake()->create();
    }
}
