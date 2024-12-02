<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    use HasFactory;

    protected $fillable = [
        'folder_id',
        'type_id',
        'product_name',
        'contact_person',
        'vendor',
        'website',
        'material_position',
        'material_description',
        'image'
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
