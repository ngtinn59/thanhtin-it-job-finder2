<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $table = 'profiles';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function education()
    {
        return $this->hasMany(educations::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class, 'profiles_id', 'id');
    }

    public function experiences()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Experience::class,'profiles_id','id');
    }

    public function projects()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Project::class,'profiles_id','id');
    }

    public function educations()  : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(educations::class,'profiles_id','id');
    }
}