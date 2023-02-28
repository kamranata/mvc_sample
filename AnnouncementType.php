<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AnnouncementType extends Model implements TranslatableContract
{
    use SoftDeletes;
    use Translatable;
    use LogsActivity;

    protected $fillable = [
        'changeable',
        'ordering',
        'status',
    ];

    public $translatedAttributes = [
        'title',
    ];

    protected $casts = [
        'changeable' => 'boolean',
        'status'     => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeDeactive($query)
    {
        return $query->where('status', false);
    }

    public function getStatusFormattedAttribute()
    {
        return $this->status ? __('Active') : __('Deactive');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // public function value($user)
    // {
    //     // $country_id = Auth::user()->company()->select('country_id')->first()->country_id;
    //     $country_id = $user->company()->select('country_id')->first()->country_id;

    //     return $this->hasOne('App\Models\AnnouncementTypeValue')->where('country_id', $country_id)->first();
    // }

    public function value()
    {
        return $this->hasOne('App\Models\AnnouncementTypeValue');
    }

    public function values()
    {
        return $this->hasMany('App\Models\AnnouncementTypeValue');
    }
}
