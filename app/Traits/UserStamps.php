<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait UserStamps
{
    public static function bootUserStamps()
    {
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {

            if (Auth::check()) {
                static::unguard();
                if (!$model->isDirty('created_by')) {
                    $model->created_by = auth()->user()->id;
                }
                if (!$model->isDirty('updated_by')) {
                    $model->updated_by = auth()->user()->id;
                }
                static::reguard();
            }
        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (Auth::check()) {
                static::unguard();
                if (!$model->isDirty('updated_by')) {
                    $model->updated_by = auth()->user()->id;
                }
                static::reguard();
            }
        });

        static::deleting(function ($model) {
            static::unguard();
            if (Auth::check()) {
                if (!$model->isDirty('deleted_by')) {
                    $model->deleted_by = auth()->user()->id;
                    $model->save();
                }
            }
            static::reguard();
        });
    }
}
