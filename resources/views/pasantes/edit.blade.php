<h1>Editar Pasante</h1>

<form action="/pasantes/{{ $pasante->id_pasante }}" method="POST">
    @csrf
    @method('PUT')

    <h3>Usuario</h3>
    <input type="text" name="nombre" value="{{ $pasante->usuario->nombre }}"><br>
    <input type="text" name="apellido" value="{{ $pasante->usuario->apellido }}"><br>
    <input type="email" name="email" value="{{ $pasante->usuario->email }}"><br>

    <h3>Pasante</h3>
    <input type="text" name="ci" value="{{ $pasante->ci }}"><br>
    <input type="text" name="reg_universitario" value="{{ $pasante->reg_universitario }}"><br>

    <button type="submit">Actualizar</button>
</form>