<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class User extends Authenticatable {
 use HasFactory, Notifiable;
 protected $fillable=['name','email','phone','role','active','password'];
 protected $hidden=['password','remember_token'];
 protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed','active'=>'boolean']; }
 public function movements(){ return $this->hasMany(VehicleMovement::class); }
 public function isAdmin(): bool { return $this->role==='admin'; }
}
