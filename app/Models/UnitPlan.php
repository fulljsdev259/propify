<?php

namespace App\Models;

use App\Traits\HasMediaTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\File;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class UnitPlan extends AuditableModel implements HasMedia
{
    use
        SoftDeletes,
        HasMediaTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'unit_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'primary',
        'unit_id',
    ];

    protected $casts = [
        'primary' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $permittedExtensions = [
        'pdf',
        'png',
        'jpg',
        'jpeg'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection('media')
            ->acceptsFile(function (File $file) {
                return in_array($file->mimeType, ['image/jpeg', 'image/png', 'application/pdf']);
            })
            ->singleFile();
    }
}
