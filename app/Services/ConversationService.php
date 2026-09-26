<?php

namespace App\Services;

use App\Models\Applicant;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mews\Purifier\Facades\Purifier;

class ConversationService
{
    // FR-MSG-01: admin memulai percakapan pribadi ke satu peserta.
    public function startFromStaff(User $participant, ?Applicant $applicant, string $subject, string $body, User $actor): Conversation
    {
        if ($applicant && $applicant->user_id !== $participant->id) {
            throw ValidationException::withMessages(['applicant_id' => 'Pendaftaran ini bukan milik peserta yang dipilih.']);
        }

        return DB::transaction(function () use ($participant, $applicant, $subject, $body, $actor) {
            $conversation = Conversation::create([
                'user_id' => $participant->id,
                'applicant_id' => $applicant?->id,
                'subject' => $subject,
                'started_by_side' => 'admin',
                'last_message_at' => now(),
            ]);

            $this->postMessage($conversation, $actor, 'admin', $body);

            return $conversation;
        });
    }

    // FR-MSG-04: peserta memulai percakapan.
    public function startFromGuardian(User $guardian, ?Applicant $applicant, string $subject, string $body): Conversation
    {
        if ($applicant && $applicant->user_id !== $guardian->id) {
            throw ValidationException::withMessages(['applicant_id' => 'Pendaftaran ini bukan milik Anda.']);
        }

        return DB::transaction(function () use ($guardian, $applicant, $subject, $body) {
            $conversation = Conversation::create([
                'user_id' => $guardian->id,
                'applicant_id' => $applicant?->id,
                'subject' => $subject,
                'started_by_side' => 'peserta',
                'last_message_at' => now(),
            ]);

            $this->postMessage($conversation, $guardian, 'peserta', $body);

            return $conversation;
        });
    }

    public function reply(Conversation $conversation, User $sender, string $side, string $body): Message
    {
        return DB::transaction(function () use ($conversation, $sender, $side, $body) {
            $message = $this->postMessage($conversation, $sender, $side, $body);
            $conversation->update(['last_message_at' => now()]);

            return $message;
        });
    }

    // database.md: sisi admin dianggap terbaca saat SALAH SATU panitia membuka percakapan (bukan per-pengguna).
    public function markReadForStaff(Conversation $conversation): void
    {
        $conversation->messages()->where('sender_side', 'peserta')->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function markReadForGuardian(Conversation $conversation): void
    {
        $conversation->messages()->where('sender_side', 'admin')->whereNull('read_at')->update(['read_at' => now()]);
    }

    private function postMessage(Conversation $conversation, User $sender, string $side, string $body): Message
    {
        return $conversation->messages()->create([
            'sender_id' => $sender->id,
            'sender_side' => $side,
            'body' => Purifier::clean($body, 'text'), // hanya teks (D-21) — profil purifier ketat, hapus semua HTML.
        ]);
    }
}
