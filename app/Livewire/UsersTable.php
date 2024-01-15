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
    public int $perPage = 10;

    #[Url('search-term')]
    public string $searchTerm = "";

    #[Url('sort_by')]
    public string $sortBy = "created_at";

    #[Url('sort_dir')]
    public string $sortDir = "DESC";

    #[Url('role')]
    public $role = "";

    #[Computed()]
    public function users()
    {
        if (!in_array($this->perPage, [5, 10, 20, 50, 100])) {
            $this->perPage = 5;
        }
        //return User::where('name','like',"%$this->searchTerm%")->orWhere('email','like',"%$this->searchTerm%")->paginate($this->perPage);
        // sürekli orWhere demek yerine bir scope oluşturduk modelde
        return User::search($this->searchTerm)->when($this->role != "", fn($query) => $query->where('is_admin', $this->role))->orderBy($this->sortBy, $this->sortDir)->paginate($this->perPage);
    }

    public function deleteUser($uuid)
    {
        try {

            $user = User::where('uuid', $uuid)->firstOrFail();
            session()->flash('success', "$user->name has been deleted successfully");
            $user->delete();
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }

    public function setSortBy($sortBy)
    {
        if ($this->sortBy == $sortBy) {
            $this->sortDir = $this->sortDir == 'DESC' ? 'ASC' : 'DESC';
        }
        $this->sortBy = $sortBy;
    }

    public function getSortIcon($columnName)
    {
        if ($this->sortBy == $columnName) {
            if ($this->sortDir == 'DESC') {
                return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
</svg>
';
            } else {
                return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
</svg>
';
            }
        }
        else{
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
</svg>
';
        }
    }

    public function render()
    {
        return view('livewire.users-table');
    }
}
