<?php
use PHPUnit\Framework\TestCase;

class GestionUsuarioTest extends TestCase
{
    private $pdo;

    // Establecemos la conexión con la base de datos
    protected function setUp(): void
    {
        // Conexión PDO a la base de datos
        $this->pdo = new PDO('mysql:host=localhost;dbname=clinica_utpl', 'root', ''); // Asegúrate de que las credenciales sean correctas
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Limpiar la base de datos después de cada prueba
    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    // Crear un usuario de prueba y verificar que se inserta correctamente
    public function testProfileViewReceivesUserData()
    {
        // Crear un usuario de prueba en la tabla 'usuario' sin el campo 'rol'
        $sql = "INSERT INTO usuario (name, email, pasword) VALUES ('Juan Pérez', 'juan.perez@example.com', 'password123')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        // Obtener el ID del último usuario insertado
        $userId = $this->pdo->lastInsertId();

        // Simular la consulta para obtener los datos del usuario
        $sql = "SELECT * FROM usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_usuario', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Comprobar que se obtienen los datos correctos
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($user);
        $this->assertEquals('Juan Pérez', $user['name']);
        $this->assertEquals('juan.perez@example.com', $user['email']);
        $this->assertEquals('password123', $user['pasword']); // Compara con el valor de la columna 'pasword'
    }
}
