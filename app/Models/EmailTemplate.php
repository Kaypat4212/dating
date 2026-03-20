<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $key
 * @property string $name
 * @property string $subject
 * @property string $body
 * @property array  $variables
 */
class EmailTemplate extends Model
{
    protected $fillable = ['key', 'name', 'subject', 'body', 'variables'];

    protected $casts = ['variables' => 'array'];

    // ── Static helpers ────────────────────────────────────────────────────────

    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }

    /**
     * Replace {placeholder} tokens in subject + body and return both.
     *
     * @param  array<string,string>  $vars  e.g. ['{user_name}' => 'Alice']
     * @return array{subject: string, html: string}
     */
    public function render(array $vars): array
    {
        return [
            'subject' => str_replace(array_keys($vars), array_values($vars), $this->subject),
            'html'    => str_replace(array_keys($vars), array_values($vars), $this->body),
        ];
    }
}
