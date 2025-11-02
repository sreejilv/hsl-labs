<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\DoctorDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.admin')]
class SurgeonList extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedUserId = null;
    public $documents = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function viewDocuments($userId)
    {
        $user = User::with('doctorDetail')->find($userId);
        if ($user && $user->doctorDetail && $user->doctorDetail->documents) {
            $this->selectedUserId = $userId;
            $this->documents = $user->doctorDetail->documents;
        }
    }

    public function toggleStatus($userId)
    {
        $user = User::with('doctorDetail')->find($userId);
        if ($user && $user->doctorDetail) {
            $user->doctorDetail->update([
                'is_active' => !$user->doctorDetail->is_active
            ]);
            
            $status = $user->doctorDetail->is_active ? 'activated' : 'deactivated';
            session()->flash('success', "Doctor has been {$status} successfully.");
        }
    }

    public function downloadDocument($filename)
    {
        $path = 'surgeon-documents/' . $filename;
        if (Storage::disk('public')->exists($path)) {
            return response()->download(Storage::disk('public')->path($path));
        }
        
        session()->flash('error', 'Document not found.');
    }

    public function closeDocumentModal()
    {
        $this->selectedUserId = null;
        $this->documents = [];
    }

    public function render()
    {
        $surgeons = User::role('surgeon')
            ->with(['doctorDetail'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('doctorDetail', function ($subQuery) {
                          $subQuery->where('doctor_name', 'like', '%' . $this->search . '%')
                                   ->orWhere('clinic_name', 'like', '%' . $this->search . '%')
                                   ->orWhere('phone', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.surgeon-list', compact('surgeons'));
    }
}
