<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logChange($model, 'created');
        });

        static::updated(function ($model) {
            self::logChange($model, 'updated');
        });

        static::deleted(function ($model) {
            self::logChange($model, 'deleted');
        });
    }

    protected static function logChange($model, string $accion)
    {
        $userId = auth()->id();

        AuditLog::create([
            'user_id' => $userId,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'accion' => $accion,
            'valores_anteriores' => $accion !== 'created' ? $model->getOriginal() : null,
            'valores_nuevos' => $accion !== 'deleted' ? $model->getAttributes() : null,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
