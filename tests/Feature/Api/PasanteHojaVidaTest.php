<?php

namespace Tests\Feature\Api;

use App\Models\HojaVida;
use App\Models\Pasante;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PasanteHojaVidaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('hoja_vida');
        Schema::dropIfExists('pasante');
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('roles');

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('descripcion');
            $table->string('abreviacion');
            $table->boolean('habilitado')->default(true);
        });

        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->string('contrasena');
            $table->unsignedInteger('id_rol');
            $table->foreign('id_rol')->references('id_rol')->on('roles');
        });

        Schema::create('pasante', function (Blueprint $table) {
            $table->increments('id_pasante');
            $table->string('ci');
            $table->string('reg_universitario');
            $table->string('direccion');
            $table->string('telefono');
            $table->unsignedInteger('id_usuario');
            $table->unsignedInteger('id_institucion')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');
        });

        Schema::create('hoja_vida', function (Blueprint $table) {
            $table->increments('id_hv');
            $table->text('habilidades')->nullable();
            $table->unsignedInteger('id_pasante');
            $table->binary('documento_blob')->nullable();
            $table->string('documento_mime')->nullable();
            $table->string('documento_nombre')->nullable();
            $table->foreign('id_pasante')->references('id_pasante')->on('pasante');
        });
    }

    public function test_pasante_can_upload_pdf_hoja_vida()
    {
        $rol = Rol::create([
            'descripcion' => 'Pasante',
            'abreviacion' => 'PAS',
            'habilitado' => true,
        ]);

        $usuario = Usuario::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@example.com',
            'telefono' => '12345678',
            'contrasena' => Hash::make('secret123'),
            'id_rol' => $rol->id_rol,
        ]);

        $pasante = Pasante::create([
            'ci' => '1234567',
            'reg_universitario' => 'RU-001',
            'direccion' => 'Calle Falsa 123',
            'telefono' => '12345678',
            'id_usuario' => $usuario->id_usuario,
            'id_institucion' => null,
        ]);

        $archivo = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

        $response = $this->actingAs($usuario, 'sanctum')
            ->post('/api/pasante/hoja-vida', [
                'habilidades' => 'Laravel, PHP, SQL',
                'documento' => $archivo,
            ]);

        $response->assertStatus(201);
        $response->assertJsonPath('hoja_vida.habilidades', 'Laravel, PHP, SQL');
        $response->assertJsonPath('hoja_vida.documento_nombre', 'cv.pdf');
        $response->assertJsonPath('hoja_vida.documento_base64', base64_encode($archivo->getContent()));

        $hojaVida = HojaVida::where('id_pasante', $pasante->id_pasante)->first();

        $this->assertNotNull($hojaVida);
        $this->assertNotNull($hojaVida->documento_blob);
        $this->assertSame('application/pdf', $hojaVida->documento_mime);
        $this->assertSame('cv.pdf', $hojaVida->documento_nombre);
        $this->assertNotNull($hojaVida->documento_base64);
    }

    public function test_pasante_can_update_pdf_hoja_vida()
    {
        $rol = Rol::create([
            'descripcion' => 'Pasante',
            'abreviacion' => 'PAS',
            'habilitado' => true,
        ]);

        $usuario = Usuario::create([
            'nombre' => 'Laura',
            'apellido' => 'González',
            'email' => 'laura@example.com',
            'telefono' => '87654321',
            'contrasena' => Hash::make('secret123'),
            'id_rol' => $rol->id_rol,
        ]);

        $pasante = Pasante::create([
            'ci' => '7654321',
            'reg_universitario' => 'RU-002',
            'direccion' => 'Avenida Siempre Viva 742',
            'telefono' => '87654321',
            'id_usuario' => $usuario->id_usuario,
            'id_institucion' => null,
        ]);

        $archivoInicial = UploadedFile::fake()->create('cv-inicial.pdf', 100, 'application/pdf');

        $this->actingAs($usuario, 'sanctum')
            ->post('/api/pasante/hoja-vida', [
                'habilidades' => 'HTML, CSS',
                'documento' => $archivoInicial,
            ]);

        $hojaVida = HojaVida::where('id_pasante', $pasante->id_pasante)->first();
        $archivoAntiguo = $hojaVida->documento_blob;

        $archivoNuevo = UploadedFile::fake()->create('cv-actualizado.pdf', 120, 'application/pdf');

        $response = $this->actingAs($usuario, 'sanctum')
            ->put('/api/pasante/hoja-vida', [
                'habilidades' => 'HTML, CSS, JavaScript',
                'documento' => $archivoNuevo,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('hoja_vida.habilidades', 'HTML, CSS, JavaScript');
        $response->assertJsonPath('hoja_vida.documento_nombre', 'cv-actualizado.pdf');
        $response->assertJsonPath('hoja_vida.documento_base64', base64_encode($archivoNuevo->getContent()));

        $hojaVida->refresh();

        $this->assertNotNull($hojaVida->documento_blob);
        $this->assertSame('application/pdf', $hojaVida->documento_mime);
    }
}
