<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testLogin()
    {

        // Could use User Factory but our setup is complicated
        // team_user, permissions, account, user, team all need to be in place
        
        // $user = User::where('username', 'lmorrison')->first();

        // $this->browse(function ($browser) use ($user) {
        //     $browser->visit('/')
        //             ->type('email', $user->email)
        //             ->type('password', 'lazwashere')
        //             ->press('SIGN IN')
        //             ->assertSee('Lazarus Morrison');
        // });
    }
}
