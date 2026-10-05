<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use Illuminate\Http\Request;
class PerfilController extends Controller { public function show(Request $request){ $user=$request->user(); $perfil=['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->role]; if($user->role==='padre') $perfil['hijos']=$user->hijos()->select('alumnos.id','legajo','nombre','apellido')->get(); if($user->role==='alumno') $perfil['alumno']=$user->alumno?->only(['id','legajo','nombre','apellido']); return response()->json($perfil); } }
