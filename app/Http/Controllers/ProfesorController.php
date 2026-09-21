<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profesor;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;


class ProfesorController extends Controller
{
    /**
     * INDEX: muestra el listado de profesores, con buscador y paginado.
     * Se ejecuta cuando el admin entra a /admin/profesores (ruta GET).
     */
    public function index(Request $request)
    {
        //Gaurdamos lo que el admin escribrio en el buscador (Si es que escribio algo)
        // $request->input('buscar') => busca dentro de la peticion (URL o formulario)
        // el valor que llego con name="buscar". Si no escribieron nada, da null.
        $buscar = $request->input('buscar');

        if($buscar){
            $profesores = Profesor::where('nombre', 'like', '%' .$buscar , '%') // Profesor-> el modelo, representa la tabla "profesores"
            // ::where(...)   -> "::" = llamo al metodo directo sobre la clase,
            // 'nombre'-> la columna donde filtro
            // 'like' -> tipo de comparacion: "que se parezca", no "que sea igual"
            // '%'.$buscar.'%' -> el texto buscado, con % (comodin = "cualquier cosa")
            ->orWhere('apellido', 'like', '%'.$buscar.'%')
            // orWhere -> condicion alternativa: si no matcheo por nombre, prueba por apellido
            ->orWhere('legajo', 'like', '%' .$buscar .'%')
            ->orderBy('apellido') // ordena el resultado alfabeticamente
            ->paginate(10); // corta el resultado en paginas de 10
        }else{
            // Si no buscó nada, traigo todos los profesores ordenados
            $profesores = Profesor::oderBy('apellido')->paginate(10);
        }
        // Retorna la vista 'profesores/index.blade.php' pasándole la lista de profesores y el texto buscado.
        return view('profesor.index', compact('profesores', 'buscar'));


    }

    /**
     * CREATE: solo muestra el formulario vacío para cargar un profesor nuevo.
     * Se ejecuta al entrar a /admin/profesores/create (ruta GET).
     */
    public function create()
    {
        return view('profesores.create'); // Me lleva a la vista donde esta mi formulario para crear el profesor
    }

    /**
     * STORE: recibe los datos del formulario de "create" y guarda el profesor.
     * Se ejecuta al enviar el formulario (ruta POST).
     */
    public function store(Request $request)
    {
        //Es el control de seguridad. Revisa que el usuario haya llenado todos los campos requeridos, que el DNI tenga 7 u 8 números, y que ni el DNI, ni el legajo, ni el email se repitan en la base de datos.
        //Si falta algo o hay un error, frena la ejecución y le muestra los mensajes en español al usuario.
        $datos = $request->validate([
            'legajo'=> 'required|string|max:20|unique:profesores,legajo',
            'dni' => 'requierd| string | regex:/^\d{7,8}$/ | unique: profesores, dni ',
            'nombre' => 'requierd| max:60',
            'apellido' => 'requierd | max:100',
            'especilidad' => 'requierd|max:100',
            'email'=> 'requierd | max:100 | unique: profesores, email',
            'telefono'=> 'requierd| string | max:30 '

        ], [
            //Mensaje de error en español que se muestran en el formulario
            'requiered' => 'El campo : attribute es obligatorio',
            'unique' => 'El :attribute ya esta regisstrado',
            'max'=> 'El campo :attribute no puede superar :max caracteres',
            'email'=>'Ingresa un correo electronico valida',
            'regex'=> 'El dni debe tener 7 u 8 digitos, sin puntos',
        ]);


        // Creamos el usuario para que el profesor pueda loguearse con su email.
        // Usamos el DNI como contraseña inicial (igual que con los alumnos).
        $usuario = User::create([
            'name' => $datos['nombre'] . ' ' .$datos['apellido'], 'email' => $datos['email'], 'password' => Hash::make($datos['dni']), 'role'=>'profesor', 
        ]);

        // Le agrego a $datos el id del usuario recien creado, para que
        // quede guardado en la fila del profesor y los dos esten vinculados.
        $datos['user_id'] = $usuario->id;

        Profesor::created($datos);

        // redirect()            -> mandá al usuario a otra página
        // ->route('admin.profesores.index') -> a la ruta que tiene ese nombre
        // ->with('success', '...') -> le agrego un mensaje de éxito para
        //   mostrar arriba de la tabla en esa página

        return redirect()->route('admin.profesores.index')->with('success', 'Profesor registrado. Credenciales → Usuario: ' . $datos['email'] . ' / Contraseña: ' . $datos['dni']);
        //Envía al usuario de vuelta a la lista principal de profesores (admin.profesores.index) y muestra un mensaje verde arriba de la tabla recordando las credenciales creadas.

    }



 /**
     * SHOW: mostraría el detalle de un solo profesor.
     * No lo usamos en este proyecto (usamos index + edit), queda vacío.
     */
    public function show(string $id)
    {
        //
    }

 /**
     * EDIT: muestra el formulario ya cargado con los datos del profesor
     * que se quiere editar.
     * Profesor $profesor -> gracias al "route model binding" de Laravel,
     * la ruta {profesor} ya me entrega el objeto completo, no un simple id.
     */
    public function edit(Profesor $profesor)

    {
        return view('profesores.edit', compact('profesor'));
        
    }

      /**
     * UPDATE: recibe los datos del formulario de edición y actualiza el registro.
     */
/**
     * MÉTODOS DE EDICIÓN: update()
     * 
     * ¿Qué hace?: Valida y guarda los datos modificados de un profesor existente.
     * Conexión: Recibe la orden desde el formulario de la vista 'edit.blade.php',
     * actualiza la base de datos y redirige a la vista principal 'index.blade.php'.
     */
    public function update(Request $request, Profesor $profesor)
    {
        // PASO 1: Validar los datos ingresados en el formulario
        $datos = $request->validate([
            
            // Rule::unique('tabla', 'columna')->ignore($profesor)
            // ¿Qué hace ignore()?: Revisa que el legajo no esté repetido en la tabla 'profesores',
            // PERO ignora el registro del profesor que estamos editando actualmente.
            // Si no pusiéramos ignore(), al guardar sin cambiar el legajo nos daría error de "ya registrado".
            'legajo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('profesores', 'legajo')->ignore($profesor),
            ],

            'dni' => [
                'required',
                'string',
                'regex:/^\d{7,8}$/',
                'max:20',
                Rule::unique('profesores', 'dni')->ignore($profesor),
            ],

            'nombre'       => 'required|string|max:100',
            'apellido'     => 'required|string|max:100',
            'especialidad' => 'required|string|max:100',

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('profesores', 'email')->ignore($profesor),
            ],

            'telefono'     => 'required|string|max:30',

        ], [
            // Mensajes de error personalizados que saltan si falla alguna regla
            'required' => 'El campo :attribute es obligatorio.',
            'unique'   => 'El :attribute ya está registrado por otro profesor.',
            'max'      => 'El campo :attribute no puede superar :max caracteres.',
            'email'    => 'Ingresá un correo electrónico válido.',
            'regex'    => 'El DNI debe tener 7 u 8 números, sin puntos.',
        ]);

        // PASO 2: Modificar los datos en la Base de Datos
        // $profesor->update($datos) toma el arreglo de datos ya validados 
        // y ejecuta la consulta SQL tipo "UPDATE profesores SET ... WHERE id = ..."
        $profesor->update($datos);

        // PASO 3: Redirección y mensaje al usuario
        // - redirect(): Le dice al navegador que navegue hacia otra página.
        // - route('admin.profesores.index'): Busca la URL asociada al nombre de esa ruta (ej: /admin/profesores).
        // - with('success', '...'): Guarda en la sesión un mensaje temporal de éxito. 
        //   La vista 'index.blade.php' lee este 'success' y muestra la alerta verde arriba de la tabla.
        return redirect()
            ->route('admin.profesores.index')
            ->with('success', 'Profesor actualizado correctamente.');
    }

    /**
     * DESTROY: NO borra al profesor de la base de datos. Es una "baja lógica":
     * solo cambia el campo activo a false, para conservar el historial
     * (igual que con Alumno).
     */
    public function destroy(Profesor $profesor)
    {
        $profesor->update(['activo' => false]);

        return redirect()
            ->route('admin.profesores.index')
            ->with('success', 'Profesor dado de baja correctamente.');
    }
}
