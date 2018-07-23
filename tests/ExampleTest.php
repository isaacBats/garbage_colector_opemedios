<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    /**
     * A basic test connect DB.
     *
     * @return json
     */
    public function testConnectionDatabase()
    {
        $fuentes = DB::table('tipo_fuente')
            ->select()
            ->get();

        $this->assertEquals($fuentes->count(), 5);
    }
}
