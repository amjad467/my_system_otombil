<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Vehicle extends Model {
 protected $fillable=['number','type','model','description','active'];
 protected $casts=['active'=>'boolean'];
 public function movements(){ return $this->hasMany(VehicleMovement::class); }
 public function activeMovement(){ return $this->hasOne(VehicleMovement::class)->where('status','out')->latestOfMany(); }
}
