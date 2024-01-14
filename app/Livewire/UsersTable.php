<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UsersTable extends Component
{

    #[Computed()]
    public function users()
    {
        return User::take(30)->get();
    }
    public function render()
    {
        return view('livewire.users-table');
    }
}
