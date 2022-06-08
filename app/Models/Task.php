<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int                     $id
 * @property      string                  $status
 * @property      int                     $priority
 * @property      string                  $title
 * @property      string                  $description
 * @property      int|null                $parent_id
 * @property      int                     $user_id
 * @property      CarbonInterface|null    $finished_at
 * @property      CarbonInterface         $created_at
 * @property      CarbonInterface|null    $updated_at
 */
class Task extends Model
{
    use HasFactory;

    protected $table = "tasks";
    protected $fillable = ['status', 'priority', 'title', 'description', 'parent_id', 'user_id'];
    protected $dates = ['finished_at', 'created_at', 'updated_at'];
    protected $guarded = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
