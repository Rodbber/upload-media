<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = ['file_name', 'extension'];

    public function fileUrl(): Attribute {
        return Attribute::make(
            get: fn () => Storage::disk('public')->url($this->file_path)
        );
    }

    public function scopeSearch(Builder $query, $search) {
        $columns = $this->fillable;

        return $query->where(function ($query) use ($columns, $search) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', "%{$search}%");
            }
        });
    }
}
