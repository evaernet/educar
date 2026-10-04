<?php
namespace App\Http\Controllers;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ProfesorController extends Controller
{
    public function index(Request $request) { $buscar=trim($request->input('buscar','')); $profesores=Profesor::query()->when($buscar!=='',fn($q)=>$q->where(fn($x)=>$x->where('nombre','like',"%$buscar%")->orWhere('apellido','like',"%$buscar%")->orWhere('legajo','like',"%$buscar%")))->orderBy('apellido')->orderBy('nombre')->paginate(10)->withQueryString(); return view('profesores.index',compact('profesores','buscar')); }
    public function create() { return view('profesores.create'); }
    public function store(Request $request) { Profesor::create($request->validate($this->rules())); return redirect()->route('admin.profesores.index')->with('success','Profesor registrado correctamente.'); }
    public function show(Profesor $profesor) { return view('profesores.show',compact('profesor')); }
    public function edit(Profesor $profesor) { return view('profesores.edit',compact('profesor')); }
    public function update(Request $request, Profesor $profesor) { $profesor->update($request->validate($this->rules($profesor))); return redirect()->route('admin.profesores.index')->with('success','Profesor actualizado correctamente.'); }
    public function destroy(Profesor $profesor) { $profesor->update(['activo'=>false]); return redirect()->route('admin.profesores.index')->with('success','Profesor dado de baja correctamente.'); }
    private function rules(?Profesor $profesor=null): array { return ['legajo'=>['required','string','max:20',Rule::unique('profesores','legajo')->ignore($profesor)],'dni'=>['required','string','max:20',Rule::unique('profesores','dni')->ignore($profesor)],'nombre'=>'required|string|max:100','apellido'=>'required|string|max:100','especialidad'=>'required|string|max:150','email'=>'nullable|email|max:255','telefono'=>'nullable|string|max:30']; }
}
