<?php
use PHPUnit\Framework\TestCase;

class CitaMedicaTest extends TestCase
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

    // Crear una cita médica de prueba y verificar que se inserta correctamente
    public function testInsertCitaMedica()
    {
        // Datos de la cita médica
        $fechaCita = '2025-02-15';
        $observacion = 'Revisión de rutina';
        $idHorario = 1; // Asume que este horario ya existe en la tabla 'horario'
        $idPaciente = 2; // Asume que este paciente ya existe en la tabla 'paciente'

        // Insertar la cita médica en la tabla 'cita_medica'
        $sql = "INSERT INTO cita_medica (fecha_cita, observacion, id_horario, id_paciente) 
                VALUES (:fecha_cita, :observacion, :id_horario, :id_paciente)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':fecha_cita', $fechaCita, PDO::PARAM_STR);
        $stmt->bindParam(':observacion', $observacion, PDO::PARAM_STR);
        $stmt->bindParam(':id_horario', $idHorario, PDO::PARAM_INT);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener el ID de la última cita insertada
        $citaId = $this->pdo->lastInsertId();

        // Simular la consulta para obtener los datos de la cita
        $sql = "SELECT * FROM cita_medica WHERE id_cita_medica = :id_cita_medica";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_cita_medica', $citaId, PDO::PARAM_INT);
        $stmt->execute();

        // Comprobar que se obtienen los datos correctos
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($cita);
        $this->assertEquals($fechaCita, $cita['fecha_cita']);
        $this->assertEquals($observacion, $cita['observacion']);
        $this->assertEquals($idHorario, $cita['id_horario']);
        $this->assertEquals($idPaciente, $cita['id_paciente']);
    }
}
