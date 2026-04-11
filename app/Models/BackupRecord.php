<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupRecord extends Model
{
    protected $fillable = [
        'filename',
        'disk',
        'path',
        'source',
        'status',
        'size_bytes',
        'file_created_at',
        'restored_at',
        'restored_by',
        'notes',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'file_created_at' => 'datetime',
        'restored_at' => 'datetime',
        'restored_by' => 'integer',
    ];
}