<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testVisitFrontPage()
    {
        return $this->visit('/')->see('Brandy.png')->see('wargame/play');
    }

    /**
     * @depends testVisitFrontPage
     */
    public function testLogin(){
        $val1 =  $this->visit('/login')->see('login')
            ->see('Gen._Ulysses_S._Grant_and_portion_of_staff,_Gen._John_A._Rawlins._-_NARA_-_524492.jpg');
        $val1->type('dave.rodal@gmail.com', 'email')
            ->type('2makegames','password')
            ->press('Login')
            ->seePageIs('/wargame/play');
    }

    /*
     * @depends testLogin
     */
    public function testVisitAdmin(){
//        $page->visit('/admin')->seePageIs('/admin');
//        $this->visit('/admin')->seePageIs('/admin');
    }
}
