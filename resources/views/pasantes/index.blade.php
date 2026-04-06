<h1>Lista de Pasantes</h1>

<a href="/pasantes/create">Crear</a>

<table border="1">
<tr>
    <th>Nombre</th>
    <th>Email</th>
    <th>CI</th>
    <th>Acciones</th>
</tr>

@foreach ($pasantes as $p)
<tr>
    <td>{{ $p->usuario->nombre }}</td>
    <td>{{ $p->usuario->email }}</td>
    <td>{{ $p->ci }}</td>

    <td>
        <a href="/pasantes/{{ $p->id_pasante }}/edit">Editar</a>

        <form action="/pasantes/{{ $p->id_pasante }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Eliminar</button>
        </form>
    </td>
</tr>
@endforeach

</table>