<?php

namespace App\Jobs;

use App\Models\Journal;
use App\Models\SyncErrorLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SyncJournalsJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $journalsChunk;
    public $publisherId;

    /**
     * Create a new job instance.
     *
     * @param array $journalsChunk
     * @param int   $publisherId
     */
    public function __construct(array $journalsChunk, int $publisherId)
    {
        $this->journalsChunk = $journalsChunk;
        $this->publisherId   = $publisherId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->journalsChunk as $data) {
            try {
                // Cek duplikat
                $exists = Journal::where('publisher_id', $this->publisherId)
                    ->where('external_id', $data['journal_id'])
                    ->exists();

                if (! $exists) {
                    $attributes = [
                        'external_id'  => $data['journal_id'],
                        'path'         => $data['path'],
                        'name'         => $data['name'],
                        'publisher_id' => $this->publisherId,
                    ];

                    // Jika ada thumbnail_url, unduh dan simpan
                    if (! is_null($data['thumbnail_url'])) {
                        $resp = Http::get($data['thumbnail_url']);
                        if ($resp->ok()) {
                            // Tentukan nama file dan foldernya
                            $ext      = pathinfo($data['thumbnail_url'], PATHINFO_EXTENSION) ?: 'jpg';
                            $filename = "thumbnails/{$this->publisherId}/". uniqid() .".{$ext}";

                            // Simpan di storage/app/public/thumbnails/...
                            Storage::disk('public')->put($filename, $resp->body());

                            // Simpan path relatif (misal: thumbnails/1/123.jpg)
                            $attributes['thumbnail'] = $filename;
                        }
                    }

                    Journal::create($attributes);
                }
            } catch (Exception $e) {
                SyncErrorLog::create([
                    'external_id'   => $data['journal_id'] ?? null,
                    'assoc'         => 'App\Models\Journal',
                    'payload'       => $data,
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }
}
