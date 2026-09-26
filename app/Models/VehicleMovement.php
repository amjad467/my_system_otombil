<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VehicleMovement extends Model {
 protected $fillable=['vehicle_id','user_id','destination','purpose','departure_time','return_time','status','notes'];
 protected $casts=['departure_time'=>'datetime','return_time'=>'datetime'];
 public function vehicle(){ return $this->belongsTo(Vehicle::class); }
 public function driver(){ return $this->belongsTo(User::class,'user_id'); }
 public function getDurationMinutesAttribute(){ if(!$this->return_time) return null; return $this->departure_time->diffInMinutes($this->return_time); }
}
