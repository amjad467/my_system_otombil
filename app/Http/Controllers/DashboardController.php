<?php
namespace App\Http\Controllers;
use App\Models\Vehicle; use App\Models\VehicleMovement; use App\Models\User; use Illuminate\Support\Carbon;
class DashboardController extends Controller { public function index(){ $today=Carbon::today(); $stats=['vehicles'=>Vehicle::where('active',true)->count(),'available'=>Vehicle::where('active',true)->whereDoesntHave('movements',fn($q)=>$q->where('status','out'))->count(),'out'=>VehicleMovement::where('status','out')->count(),'today'=>VehicleMovement::whereDate('departure_time',$today)->count(),'drivers'=>User::where('role','driver')->where('active',true)->count()]; $recent=VehicleMovement::with(['vehicle','driver'])->latest('departure_time')->limit(10)->get(); return view('dashboard',compact('stats','recent')); } }
