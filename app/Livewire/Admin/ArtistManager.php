<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Artist;
use Illuminate\Support\Facades\Storage;

class ArtistManager extends Component
{
    use WithFileUploads, WithPagination;

    // Create form
    public bool $showCreateModal = false;
    public string $createName = '';
    public string $createBio = '';
    public $createPhoto;

    // Edit form
    public bool $showEditModal = false;
    public ?Artist $editingArtist = null;
    public array $editForm = [
        'name' => '',
        'bio' => '',
    ];
    public $editPhoto;

    // Delete
    public bool $showDeleteModal = false;
    public ?int $deletingArtistId = null;
    public string $deletingArtistName = '';

    // Search
    public string $search = '';

    protected $rules = [
        'createName' => 'required|string|max:255',
        'createBio' => 'nullable|string|max:2000',
        'createPhoto' => 'nullable|image|max:5120',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // ============ CREATE ============
    public function openCreateModal()
    {
        $this->reset(['createName', 'createBio', 'createPhoto']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['createName', 'createBio', 'createPhoto']);
    }

    public function createArtist()
    {
        $this->validate([
            'createName' => 'required|string|max:255',
            'createBio' => 'nullable|string|max:2000',
            'createPhoto' => 'nullable|image|max:5120',
        ]);

        $photoPath = null;
        if ($this->createPhoto) {
            $filename = 'artists/' . uniqid() . '_' . $this->createPhoto->getClientOriginalName();
            
            try {
                Storage::disk('azure')->put($filename, file_get_contents($this->createPhoto->getRealPath()));
                $photoPath = $filename;
            } catch (\Exception $e) {
                // Fallback to local
                $photoPath = $this->createPhoto->store('artists', 'public');
            }
        }

        Artist::create([
            'name' => $this->createName,
            'bio' => $this->createBio,
            'photo' => $photoPath,
        ]);

        $this->closeCreateModal();
        session()->flash('success', 'Artist created successfully!');
    }

    // ============ EDIT ============
    public function editArtist(int $id)
    {
        $this->editingArtist = Artist::find($id);
        
        if (!$this->editingArtist) {
            return;
        }

        $this->editForm = [
            'name' => $this->editingArtist->name,
            'bio' => $this->editingArtist->bio ?? '',
        ];
        $this->editPhoto = null;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingArtist = null;
        $this->editPhoto = null;
    }

    public function updateArtist()
    {
        $this->validate([
            'editForm.name' => 'required|string|max:255',
            'editForm.bio' => 'nullable|string|max:2000',
            'editPhoto' => 'nullable|image|max:5120',
        ]);

        if (!$this->editingArtist) {
            return;
        }

        $photoPath = $this->editingArtist->photo;
        
        if ($this->editPhoto) {
            // Delete old photo
            if ($photoPath) {
                try {
                    Storage::disk('azure')->delete($photoPath);
                } catch (\Exception $e) {
                    Storage::disk('public')->delete($photoPath);
                }
            }

            // Upload new photo
            $filename = 'artists/' . uniqid() . '_' . $this->editPhoto->getClientOriginalName();
            try {
                Storage::disk('azure')->put($filename, file_get_contents($this->editPhoto->getRealPath()));
                $photoPath = $filename;
            } catch (\Exception $e) {
                $photoPath = $this->editPhoto->store('artists', 'public');
            }
        }

        $this->editingArtist->update([
            'name' => $this->editForm['name'],
            'bio' => $this->editForm['bio'],
            'photo' => $photoPath,
        ]);

        $this->closeEditModal();
        session()->flash('success', 'Artist updated successfully!');
    }

    // ============ DELETE ============
    public function confirmDelete(int $id)
    {
        $artist = Artist::find($id);
        if ($artist) {
            $this->deletingArtistId = $id;
            $this->deletingArtistName = $artist->name;
            $this->showDeleteModal = true;
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingArtistId = null;
        $this->deletingArtistName = '';
    }

    public function deleteArtist()
    {
        $artist = Artist::find($this->deletingArtistId);
        
        if ($artist) {
            // Delete photo from storage
            if ($artist->photo) {
                try {
                    Storage::disk('azure')->delete($artist->photo);
                } catch (\Exception $e) {
                    Storage::disk('public')->delete($artist->photo);
                }
            }

            $artist->delete();
            session()->flash('success', 'Artist deleted successfully!');
        }

        $this->closeDeleteModal();
    }

    public function render()
    {
        $artists = Artist::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->withCount(['songs', 'albums'])
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.artist-manager', [
            'artists' => $artists,
        ]);
    }
}
