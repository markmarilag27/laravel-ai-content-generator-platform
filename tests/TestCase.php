<?php

namespace Tests;

use App\Traits\Database\InteractsWithPostgresRls;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithPostgresRls;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setPostgresContext();
    }
}
