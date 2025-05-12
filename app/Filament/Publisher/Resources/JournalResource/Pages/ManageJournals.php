<?php

namespace App\Filament\Publisher\Resources\JournalResource\Pages;

use Filament\Actions;
use App\Models\Journal;
use App\Jobs\SyncJournalsJob;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Publisher\Resources\JournalResource;

class ManageJournals extends ManageRecords
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->visible(fn() => auth()->user()->can('sync_journal'))
                ->action(fn() => static::sync())
                ->icon('heroicon-m-arrow-path')
                ->label('Sinkron OJS'),
        ];
    }

    private static function sync()
    {
        $lastExternalId = Journal::where('publisher_id', auth()->user()->publisher->id)->max('external_id') ?? 0;

        $response = Http::withOptions([
            'verify' => !config('app.debug'),
        ])->get(auth()->user()->publisher->ojs_driver_url . '/api/ojs/journals');
        if ($response->failed()) {
            Notification::make()
                ->title('Gagal mengambil data journal!')
                ->danger()
                ->send();
            return;
        }

        $journals = collect($response->json()['data'])
            ->where('journal_id', '>', $lastExternalId)
            ->values();

        $total = $journals->count();
        $chunks = $journals->chunk(100);
        $chunks->each(fn($chunk) => SyncJournalsJob::dispatch($chunk->toArray(), auth()->user()->publisher->id));

        Notification::make()
            ->title("{$total} jurnal siap untuk sinkronisasi.")
            ->success()
            ->send();

        return;
    }
}
