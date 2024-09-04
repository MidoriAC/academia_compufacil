<?php

namespace App\Models\Course;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseSection extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "name",
        "course_id",
        "state",
    ];

    public function setCreatedAtAttribute($value)
    {
        date_default_timezone_set("America/Lima");
        $this->attributes["created_at"] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value)
    {
        date_default_timezone_set("America/Lima");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function clases()
    {
        return $this->hasMany(CourseClase::class,"course_section_id");
    }

    function AddTimes1($horas)
    {
        $total = 0;
        foreach($horas as $h) {
            $parts = explode(":", $h);
            $total += $parts[2] + $parts[1]*60 + $parts[0]*3600;
        }
        $hours = floor($total / 3600);
        $minutes = floor(($total / 60) % 60);
        $seconds = $total % 60;

        return $hours." hrs ".$minutes." mins";
    }
    function AddTimes($horas)
    {
        $total = 0;
        foreach($horas as $h) {
            $parts = explode(":", $h);
            
            // Asegurarse de que al menos hay horas y minutos
            $hours = isset($parts[0]) ? (int)$parts[0] : 0;
            $minutes = isset($parts[1]) ? (int)$parts[1] : 0;
            $seconds = isset($parts[2]) ? (int)$parts[2] : 0;
    
            $total += $seconds + $minutes*60 + $hours*3600;
        }
        $hours = floor($total / 3600);
        $minutes = floor(($total / 60) % 60);
        $seconds = $total % 60;
    
        return $hours." hrs ".$minutes." mins";
    }
    public function getTimeSectionAttribute()
    {
       $times = [];
        foreach ($this->clases as $keyC => $clase) {
            array_push($times,$clase->time);
        }
       return $this->AddTimes($times);
    }
}
