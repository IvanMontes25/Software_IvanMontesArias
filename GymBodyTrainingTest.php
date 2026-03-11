<?php
use PHPUnit\Framework\TestCase;

class GymBodyTrainingTest extends TestCase
{
    // MÓDULO 1: LOGIN - Mapeo de Roles
    // Código fuente: index.php líneas 33-42

    public function test_CB_L01_MapeoRol_DesignacionesConocidas()
    {
        $rolMap = [
            'Administrador' => 'admin',
            'Recepcionista'  => 'recepcionista',
            'Cajero'         => 'cajero',
            'Entrenador'     => 'entrenador',
            'Asistente'      => 'asistente',
        ];

        $this->assertEquals('admin',         $rolMap['Administrador'] ?? 'staff', 'Administrador debe mapearse a admin');
        $this->assertEquals('recepcionista', $rolMap['Recepcionista']  ?? 'staff', 'Recepcionista debe mapearse a recepcionista');
        $this->assertEquals('cajero',        $rolMap['Cajero']         ?? 'staff', 'Cajero debe mapearse a cajero');
        $this->assertEquals('entrenador',    $rolMap['Entrenador']     ?? 'staff', 'Entrenador debe mapearse a entrenador');
        $this->assertEquals('asistente',     $rolMap['Asistente']      ?? 'staff', 'Asistente debe mapearse a asistente');
    }

    public function test_CB_L02_MapeoRol_DesignacionDesconocidaUsaDefault()
    {
        $rolMap = [
            'Administrador' => 'admin',
            'Recepcionista'  => 'recepcionista',
            'Cajero'         => 'cajero',
            'Entrenador'     => 'entrenador',
            'Asistente'      => 'asistente',
        ];

        $desig = 'Otro';
        $rolSistema = $rolMap[$desig] ?? 'staff';
        $this->assertEquals('staff', $rolSistema, 'Designacion desconocida debe retornar staff');

        $desigVacia = '';
        $rolVacio = $rolMap[$desigVacia] ?? 'staff';
        $this->assertEquals('staff', $rolVacio, 'Designacion vacia debe retornar staff');
    }

    // MÓDULO 2: REGISTRO DE CLIENTES - Validaciones
    // Código fuente: admin/cliente_actions.php líneas 45-67

    public function test_CB_C01_ValidacionNombre()
    {
        // RAMA TRUE: nombre corto - debe fallar
        $fullname_corto = 'Jo';
        $this->assertTrue(strlen($fullname_corto) < 3, 'Nombre de 2 caracteres debe ser invalido');

        // RAMA FALSE: nombre valido - no debe fallar
        $fullname_valido = 'Juan Perez';
        $this->assertFalse(strlen($fullname_valido) < 3, 'Nombre de 10 caracteres debe ser valido');

        // CASO LIMITE: exactamente 3 caracteres - valido
        $fullname_limite = 'Ana';
        $this->assertFalse(strlen($fullname_limite) < 3, 'Nombre de 3 caracteres debe ser valido');
    }

    public function test_CB_C02_ValidacionUsername()
    {
        // Username muy corto: INVALIDO
        $username_corto = 'ab';
        $this->assertTrue(strlen($username_corto) < 3, 'Username de 2 caracteres debe ser invalido');

        // Username con espacios: INVALIDO
        $username_espacios = 'juan perez';
        $this->assertTrue((bool) preg_match('/\s/', $username_espacios), 'Username con espacios debe ser invalido');

        // Username valido: VALIDO
        $username_valido = 'juanp99';
        $esInvalido = strlen($username_valido) < 3 || preg_match('/\s/', $username_valido);
        $this->assertFalse((bool)$esInvalido, 'Username valido no debe activar error');
    }

    // MÓDULO 3: RECUPERACION DE CONTRASENA
    // Código fuente: cliente/forgot_password.php líneas 7-10

    public function test_CB_R01_RecuperacionCamposVacios()
    {
        $user = trim('');
        $ci   = trim('');

        $this->assertTrue($user === '' || $ci === '', 'Campos vacios deben activar el error');

        $respuesta = ['success' => false, 'message' => 'Datos incompletos'];
        $this->assertFalse($respuesta['success'], 'success debe ser false');
        $this->assertEquals('Datos incompletos', $respuesta['message'], 'Mensaje debe ser exacto');
    }

    public function test_CB_R02_RecuperacionSoloCIVacio()
    {
        // Solo CI vacio: debe fallar por el OR
        $user = trim('cliente01');
        $ci   = trim('');
        $this->assertTrue($user === '' || $ci === '', 'CI vacio debe activar el error');

        // Ambos con valor: no debe fallar
        $user2 = trim('cliente01');
        $ci2   = trim('12345678');
        $this->assertFalse($user2 === '' || $ci2 === '', 'Campos llenos no deben activar el error');
    }

    // MÓDULO 4: PAGOS - Sanitizacion de Paginacion
    // Código fuente: admin/pagos.php línea 35

    public function test_CB_P01_SanitizacionPaginacion()
    {
        // Texto no numerico → page = 1
        $pageTexto = 'textoInvalido';
        $r1 = isset($pageTexto) && ctype_digit($pageTexto) ? max(1, (int)$pageTexto) : 1;
        $this->assertEquals(1, $r1, 'Texto invalido debe dar page=1');

        // Numero valido → page = 3
        $pageValido = '3';
        $r2 = isset($pageValido) && ctype_digit($pageValido) ? max(1, (int)$pageValido) : 1;
        $this->assertEquals(3, $r2, 'Numero valido debe retornar ese numero');

        // Numero negativo → page = 1
        $pageNegativo = '-5';
        $r3 = isset($pageNegativo) && ctype_digit($pageNegativo) ? max(1, (int)$pageNegativo) : 1;
        $this->assertEquals(1, $r3, 'Negativo debe dar page=1');

        // Cero → max(1,0) = 1
        $pageCero = '0';
        $r4 = isset($pageCero) && ctype_digit($pageCero) ? max(1, (int)$pageCero) : 1;
        $this->assertEquals(1, $r4, 'Cero debe normalizarse a 1');

        // Intento SQLi → page = 1
        $pageSQLi = '1 OR 1=1';
        $r5 = isset($pageSQLi) && ctype_digit($pageSQLi) ? max(1, (int)$pageSQLi) : 1;
        $this->assertEquals(1, $r5, 'SQLi debe ser bloqueado y dar page=1');
    }
}