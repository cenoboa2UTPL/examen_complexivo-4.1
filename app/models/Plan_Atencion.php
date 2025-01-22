<?php 
namespace models;
use report\implementacion\Model;

class Plan_Atencion extends Model
{
    protected string $Table = "atencion_medica ";

    protected $Alias = "as plan ";

    protected string $PrimayKey = "id_atencion_medica";

     /// modificando para el hosting
    /// registrar la atencion médica del paciente
    public function guardar(string $antecedente,string $tiempo_enfermedad,string|null $alergias,string|null $interv_quir,string $vacuna,
                           string $examen_fisico,string|null $diagnostico,string $analisisConfirm,string|null $desc_analisis,
                           string|null $plan_tratamiento,string|null $desc_tratamiento,$triaje,$fecha_procima_cita,
                           string|null $ant_medicos,string|null $ant_traumaticos,string|null $ant_gineco_obs,
                           string|null $ant_familiares,string $fuma,string $bebe,string|null $otros)
    {
        return $this->Insert([
            "antecedentes"=>$antecedente,
            "tiempo_enfermedad"=>$tiempo_enfermedad,
            "alergias"=>$alergias,
            "intervensiones_quirugicas"=>$interv_quir,
            "vacunas_completos"=>$vacuna,
            "resultado_examen_fisico"=>$examen_fisico,
            "diagnostico"=>$diagnostico,
            "requiere_analisis"=>$analisisConfirm,
            "desc_analisis_requerida"=>$desc_analisis,
            "plan_tratamiento"=>$plan_tratamiento,
            "desc_plan"=>$desc_tratamiento,
            "proxima_cita"=>$fecha_procima_cita,
            "id_triaje"=>$triaje,
            "ant_medicos" => $ant_medicos,
            "ant_traumaticos" => $ant_traumaticos,
            "ant_gineco_obs" => $ant_gineco_obs,
            "ant_familiares" => $ant_familiares,
            "fuma" => $fuma,
            "bebe" => $bebe,
            "otros" => $otros
        ]);
    }
}