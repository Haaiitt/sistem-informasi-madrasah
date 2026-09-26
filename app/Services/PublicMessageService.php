<?php

namespace App\Services;

use App\Models\PublicMessage;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Mews\Purifier\Facades\Purifier;

class PublicMessageService
{
    public function submit(array $data): PublicMessage
    {
        return PublicMessage::create([
            'sender_name' => $data['sender_name'],
            'contact_phone' => $data['contact_phone'],
            'category' => $data['category'],
            'body' => Purifier::clean($data['body'], 'text'),
            'status' => 'baru',
        ]);
    }

    // AZ-33: pembaruan bersyarat (WHERE status = 'baru') agar dua panitia tidak saling menimpa.
    public function markHandled(PublicMessage $message, ?string $note, User $actor): void
    {
        $updated = PublicMessage::where('id', $message->id)
            ->where('status', 'baru')
            ->update([
                'status' => 'ditangani',
                'handled_by' => $actor->id,
                'handled_at' => now(),
                'handling_note' => $note,
            ]);

        if ($updated === 0) {
            throw ValidationException::withMessages(['status' => 'Pesan ini sudah ditangani oleh panitia lain.']);
        }
    }
}
