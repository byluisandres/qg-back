<?php

namespace App\Models;

use App\Traits\QueryByContext;
use App\Traits\UserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Category extends Model
{
    use HasFactory,QueryByContext, SoftDeletes;
    protected $fillable = ['name', 'color','created_by'];

    // Update fields
    const UPDATE_FIELDS = ['name', 'color'];

    /**
     * Reglas de validación
     */
    public static function rules($id = -1): array
    {
        return [
            'name' => ['required', 'string', Rule::unique('categories')->ignore($id,'id,delete_at')],
        ];
    }

    // relación
    public function types(): HasMany
    {
        return $this->hasMany(Types::class);
    }

    // buscar
    protected static $searchColumns = ['id', 'name'];
}
