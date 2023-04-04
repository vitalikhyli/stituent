<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;


class GroupsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testCreateGroup()
    {
        
        // $this->browse(function (Browser $browser) {
        //     $browser->loginAs(User::where('username', 'lmorrison')->first());

        //     $browser->visit('/office')
        //             ->assertSee('Groups');

        //     $browser->clickLink('Groups')
        //             ->assertSee('Categories');

        //     $browser->press('Add Group')
        //             ->type('name', 'My Awesome Test Group')
        //             ->press('Add New')
        //             ->assertSee('My Awesome Test Group');
        // });
    }
}
