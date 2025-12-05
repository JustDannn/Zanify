<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\DataStructures\DoublyLinkedList; 
use App\DataStructures\CircularDoublyLinkedList;
use App\DataStructures\Node;

class AdminController extends Controller
{
    private $accountName;
    private $accountKey;
    private $containerName;
    private $endpointSuffix;
    private $azureBaseUrl;

    public function __construct()
    {
        $this->accountName = env('AZURE_ACCOUNT_NAME');
        $this->accountKey = env('AZURE_ACCOUNT_KEY');
        $this->containerName = env('AZURE_CONTAINER_NAME');
        $this->endpointSuffix = env('AZURE_ENDPOINT_SUFFIX');
        
        if ($this->accountName) {
            $this->azureBaseUrl = "https://{$this->accountName}.blob.{$this->endpointSuffix}/{$this->containerName}/";
        }
    }
    
    private function loadStructure(Request $request, $key, $defaultObject)
    {
        if ($request->session()->has($key)) {
            $serialized = $request->session()->get($key);
            try {
                 $unserializedObject = unserialize($serialized, ['allowed_classes' => true]);
                 if ($unserializedObject instanceof $defaultObject) return $unserializedObject;
            } catch (\Exception $e) {}
        }
        return $defaultObject;
    }

    public function index(Request $request)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());
        return view('admin.library', [
            'songs' => $library->getAllSongs(),
            'total' => $library->count,
            'azure_base_url' => $this->azureBaseUrl
        ]);
    }

    // --- FITUR 1: SYNC DARI AZURE ---
    public function syncFromAzure(Request $request)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());
        
        $date = gmdate('D, d M Y H:i:s T');
        $url = "https://{$this->accountName}.blob.{$this->endpointSuffix}/{$this->containerName}?restype=container&comp=list";
        
        $canonicalizedHeaders = "x-ms-date:$date\nx-ms-version:2020-04-08";
        $canonicalizedResource = "/{$this->accountName}/{$this->containerName}\ncomp:list\nrestype:container";
        
        $stringToSign = "GET\n\n\n\n\n\n\n\n\n\n\n\n$canonicalizedHeaders\n$canonicalizedResource";
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($this->accountKey), true));

        $response = Http::withoutVerifying()->withHeaders([
            'x-ms-date' => $date,
            'x-ms-version' => '2020-04-08',
            'Authorization' => "SharedKey {$this->accountName}:$signature",
        ])->get($url);

        if (!$response->successful()) {
            return redirect('/admin/library')->with('error', 'Gagal connect ke Azure. Status: ' . $response->status());
        }

        $xml = simplexml_load_string($response->body());
        $countAdded = 0;

        foreach ($xml->Blobs->Blob as $blob) {
            $fileName = (string)$blob->Name;
            
            if (strpos(strtolower($fileName), '.mp3') === false) continue;
            if ($this->isFileExistInLibrary($library, $fileName)) continue;

            $fullAudioUrl = $this->azureBaseUrl . rawurlencode($fileName);
            
            $parts = explode(' - ', str_replace('.mp3', '', $fileName));
            $artist = count($parts) > 1 ? $parts[0] : 'Unknown Artist';
            $title = count($parts) > 1 ? $parts[1] : str_replace('.mp3', '', $fileName);

            $songData = [
                'id' => uniqid(),
                'title' => $title,
                'artist' => $artist,
                'album' => 'Azure Import',
                'genre' => 'Pop',
                'duration' => 'Unknown',
                'audio_url' => $fullAudioUrl
            ];

            $library->addSong($songData);
            $countAdded++;
        }

        $request->session()->put('library', serialize($library));

        if ($countAdded > 0) {
            return redirect('/admin/library')->with('success', "Berhasil sinkronisasi! $countAdded lagu baru ditambahkan.");
        } else {
            return redirect('/admin/library')->with('success', "Sinkronisasi selesai. Semua lagu di Azure sudah ada di library.");
        }
    }

    private function isFileExistInLibrary($library, $fileName) {
        $encodedUrl = $this->azureBaseUrl . rawurlencode($fileName);
        $current = $library->head;
        while ($current !== null) {
            if ($current->data['audio_url'] === $encodedUrl) return true;
            $current = $current->next;
        }
        return false;
    }

    // --- FITUR 2: ADD & UPLOAD ---
    public function add(Request $request)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());
        
        if ($request->isMethod('post')) {
            $inputType = $request->input('input_type');
            $finalFileName = '';

            if ($inputType === 'upload' && $request->hasFile('audio_file')) {
                $file = $request->file('audio_file');
                $finalFileName = $file->getClientOriginalName();
                $fileContent = file_get_contents($file->getRealPath());
                
                if (!$this->uploadToAzure($finalFileName, $fileContent, strlen($fileContent), $file->getMimeType())) {
                    return redirect()->back()->with('error', 'Gagal upload ke Azure.');
                }
            } elseif ($inputType === 'manual' && $request->input('audio_filename')) {
                $finalFileName = $request->input('audio_filename');
            } else {
                return redirect()->back()->with('error', 'Mohon pilih file.');
            }

            $fullAudioUrl = $this->azureBaseUrl . rawurlencode($finalFileName);
            
            $songData = [
                'id' => uniqid(),
                'title' => $request->input('title'),
                'artist' => $request->input('artist'),
                'album' => $request->input('album'),
                'genre' => $request->input('genre'),
                'duration' => $request->input('duration'),
                'audio_url' => $fullAudioUrl
            ];
            
            $library->addSong($songData);
            $request->session()->put('library', serialize($library));
            
            return redirect('/admin/library')->with('success', 'Lagu berhasil ditambahkan!');
        }
        return redirect('/admin/library');
    }

    private function uploadToAzure($blobName, $content, $length, $type)
    {
        $date = gmdate('D, d M Y H:i:s T');
        $url = $this->azureBaseUrl . rawurlencode($blobName);
        
        $canonicalizedHeaders = "x-ms-blob-type:BlockBlob\nx-ms-date:$date\nx-ms-version:2020-04-08";
        $canonicalizedResource = "/{$this->accountName}/{$this->containerName}/" . rawurlencode($blobName);
        
        $stringToSign = "PUT\n\n\n$length\n\n$type\n\n\n\n\n\n\n$canonicalizedHeaders\n$canonicalizedResource";
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($this->accountKey), true));

        return Http::withoutVerifying()->withHeaders([
            'x-ms-date' => $date,
            'x-ms-version' => '2020-04-08',
            'x-ms-blob-type' => 'BlockBlob',
            'Authorization' => "SharedKey {$this->accountName}:$signature",
            'Content-Type' => $type,
            'Content-Length' => $length
        ])->withBody($content, $type)->put($url)->successful();
    }

    // --- FITUR 3: EDIT & UPDATE (INI YANG KEMBALI) ---
    public function edit(Request $request, $id)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());
        
        // Cari lagu di DLL
        $song = $library->findSongById($id);
        
        if (!$song) {
            return redirect('/admin/library')->with('error', 'Lagu tidak ditemukan.');
        }

        return view('admin.edit', [
            'song' => $song,
            'azure_base_url' => $this->azureBaseUrl
        ]);
    }

    public function update(Request $request, $id)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());

        if ($request->isMethod('post')) {
            // Data baru dari form edit
            $updatedData = [
                'title' => $request->input('title'),
                'artist' => $request->input('artist'),
                'album' => $request->input('album'),
                'genre' => $request->input('genre'),
                'duration' => $request->input('duration'),
            ];

            // Update di Linked List
            $success = $library->updateSong($id, $updatedData);

            if ($success) {
                $request->session()->put('library', serialize($library));
                return redirect('/admin/library')->with('success', 'Identitas lagu berhasil diperbarui.');
            } else {
                return redirect('/admin/library')->with('error', 'Gagal memperbarui lagu.');
            }
        }
        return redirect('/admin/library');
    }

    // --- FITUR 4: DELETE & SYNC PLAYLIST ---
    public function delete(Request $request, $id)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());
        $playlist = $this->loadStructure($request, 'my_playlist', new CircularDoublyLinkedList());

        $isDeletedFromLibrary = $library->deleteSongById($id);
        $playlist->deleteSongById($id);

        $request->session()->put('library', serialize($library));
        $request->session()->put('my_playlist', serialize($playlist));

        return $isDeletedFromLibrary 
            ? redirect('/admin/library')->with('success', 'Lagu dihapus.') 
            : redirect('/admin/library')->with('error', 'ID tidak ditemukan.');
    }
}