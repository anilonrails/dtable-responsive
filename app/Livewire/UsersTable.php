<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{

    use WithPagination;


    #[Url('p')]
    public int $perPage=10;

    #[Url('search-term')]
    public string $searchTerm="";

    #[Url('role')]
    public  $role="";
    #[Computed()]
    public function users()
    {
        if (!in_array($this->perPage,[5,10,20,50,100]))
        {
            $this->perPage=5;
        }
        //return User::where('name','like',"%$this->searchTerm%")->orWhere('email','like',"%$this->searchTerm%")->paginate($this->perPage);
        // sürekli orWhere demek yerine bir scope oluşturduk modelde
        return User::search($this->searchTerm)->when($this->role != "",fn($query)=> $query->where('is_admin',$this->role) )->paginate($this->perPage);
    }

    public function deleteUser($uuid){
        try {

        $user = User::where('uuid',$uuid)->firstOrFail();
        session()->flash('success',"$user->name has been deleted successfully");
        $user->delete();
        }catch (ModelNotFoundException $e)
        {
            abort(404);
        }
    }
    public function render()
    {
        return view('livewire.users-table');
    }
}
