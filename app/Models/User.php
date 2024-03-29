<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'sekolah',
        'nim',
        'kelas',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function akses_divisi()
    {
        // return $this->belongsToMany(Divisi::class,'akses_divisi','divisi_id','user_id');
        return $this->hasMany(Akses_divisi::class,'user_id');
    }
    public function akses_program()
    {
        // return $this->belongsToMany(Program::class,'akses_program','program_id','user_id');
        return $this->hasMany(Akses_program::class,'user_id');
    }
    public function talents()
    {
        return $this->belongsToMany(Talent::class)->withPivot('score');
    }

    public function getDivisionForProgram($program)
    {      
        return Akses_divisi::where('akses_divisi.user_id', $this->id)
            ->join('divisi','divisi.id','akses_divisi.divisi_id')
            ->join('program','program.id','divisi.program_id')
            ->where('program.id', $program)
            ->first();
    }
    
    
}
