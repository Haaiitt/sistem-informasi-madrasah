<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['name', 'value', 'updated_by'];

    // FR-KON-07: dibaca di mana saja tanpa perlu query berulang per key.
    public static function getValue(string $name): ?string
    {
        return static::where('name', $name)->value('value');
    }
}
