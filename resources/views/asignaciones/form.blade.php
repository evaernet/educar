@foreach([['ciclo_lectivo_id','Ciclo lectivo',$ciclos],['curso_id','Curso',$cursos],['materia_id','Materia',$materias],['profesor_id','Profesor',$profesores]] as [$campo,$label,$items])
    <div>
        <label class="block font-medium mb-1">{{ $label }}</label>
        <select name="{{ $campo }}" required class="w-full border rounded px-3 py-2">
            <option value="">Seleccionar</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}" @selected(old($campo, $asignacion->$campo ?? '') == $item->id)>
                    {{ $campo === 'ciclo_lectivo_id' ? $item->anio : ($campo === 'curso_id' ? $item->nivel->nombre.' - '.$item->nombre.' '.$item->division : ($campo === 'profesor_id' ? $item->apellido.', '.$item->nombre : $item->nombre)) }}
                </option>
            @endforeach
        </select>
        @error($campo)<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>
@endforeach
