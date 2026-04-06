<form action="/pasantes" method="POST">
    @csrf

    <h3>Usuario</h3>
    <input type="text" name="nombre" placeholder="Nombre">
    <input type="text" name="apellido" placeholder="Apellido">
    <input type="email" name="email" placeholder="Email">

    <h3>Pasante</h3>
    <input type="text" name="ci" placeholder="CI">
    <input type="text" name="reg_universitario" placeholder="Registro">

    <button type="submit">Guardar</button>
</form>