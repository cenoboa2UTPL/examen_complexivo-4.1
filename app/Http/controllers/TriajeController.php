<?php 
namespace Http\controllers;

use Http\pageextras\PageExtra;
use lib\BaseController;
use models\{CitaMedica, Triaje};


class TriajeController extends BaseController
{
    private static $ModelPacientesTriaje,$Model;
    
    /// mostrar la vista de pacientes que pasan a triaje
    public static function index()
    {
        self::NoAuth();

        if(self::profile()->rol === self::$profile[4] or self::profile()->rol === self::$profile[3])
        {
            self::View_("paciente.triaje");
        }
        else
        {
            PageExtra::PageNoAutorizado();
        }
    }

    /// mostrar pacientes que pasan a triaje por día
    public static function mostrarPacientesTriaje()
    {
        self::NoAuth();
        if(self::ValidateToken(self::get("token_")))
        {
            self::$ModelPacientesTriaje = new CitaMedica;
            /// mostramos a todos los pacientes que pasan directo a triaje
            if(self::profile()->rol === 'Médico')
            {
                # capturamos el id de la persona authenticado
                $Persona_Id = self::profile()->id_persona;
                $Pacientes_Triaje = self::$ModelPacientesTriaje->procedure("proc_pacientes_triaje","c",[$Persona_Id,self::FechaActual("Y-m-d"),self::FechaActual("H:i:s")]);
            }
            else
            {
                $Pacientes_Triaje = self::$ModelPacientesTriaje->procedure("proc_pacientes_triaje","c",[null,self::FechaActual("Y-m-d"),self::FechaActual("H:i:s")]);
            }

            self::json(['response'=>$Pacientes_Triaje]);
        }
    }

    /// registrar pacientes que sacaron una cita mèdica a triaje
    public static function save()
    {
        self::NoAuth();
        if(self::ValidateToken(self::post("token_")))
        {
            self::$Model = new Triaje;
            self::$ModelPacientesTriaje = new CitaMedica;

            $resultado = self::$Model->Insert([
                "presion_arterial"=>self::post("presion_arterial"),
                "temperatura"=>self::post("temperatura"),
                "frecuencia_cardiaca"=>self::post("frecuencia_cardiaca"),
                "frecuencia_respiratoria"=>self::post("frecuencia_resp"),
                "saturacion_oxigeno"=>self::post("saturacion_oxigeno"),
                "talla"=>self::post("talla"),
                "peso"=>self::post("peso"),
                "imc"=>self::post("imc"),
                "estado_imc"=>self::post("estado_imc"),
                "id_cita_medica"=>self::post("cita_id")
            ]);

            if($resultado)
            {
                self::$ModelPacientesTriaje->Update([
                    "id_cita_medica"=>self::post("cita_id"),
                    "estado"=>"examinado"
                ]);
                self::json(['response'=>'ok']);
            }
            else
            {
                self::json(['response'=>'error']);
            }
        }
    }

    /// consultar triaje por cita médica
    public static function consulta_triaje($cita)
    {
        self::NoAuth();
        if(self::ValidateToken(self::get("token_")))
        {
            self::$Model = new Triaje;
            $data_ = self::$Model->query()->Where("id_cita_medica","=",$cita)->first();
            self::json(['response'=>$data_]);
        }
    }

    /// actualziar triaje de un paciente 
    public static function update($triaje_id)
    {
        self::NoAuth();

        if(self::ValidateToken(self::post("token_")))
        {
            self::$Model = new Triaje;

            $resultado = self::$Model->Update([
                "id_triaje"=>$triaje_id,
                "presion_arterial"=>self::post("presion_arterial"),
                "temperatura"=>self::post("temperatura"),
                "frecuencia_cardiaca"=>self::post("frecuencia_cardiaca"),
                "frecuencia_respiratoria"=>self::post("frecuencia_resp"),
                "saturacion_oxigeno"=>self::post("saturacion_oxigeno"),
                "talla"=>self::post("talla"),
                "peso"=>self::post("peso"),
                "imc"=>self::post("imc"),
                "estado_imc"=>self::post("estado_imc"),
            ]);

            if($resultado)
            {
                self::json(['response'=>'ok']);
            }
            else
            {
                self::json(['response'=>'error']);
            }
            
        }
    }
    public static function PrintFechaText(string $fecha)
    {
      if(self::ValidateToken(self::get("token_")))
      {
        $Dia = self::getDayDate($fecha);
         if($fecha === self::FechaActual("Y-m-d"))
         {
            $fecha = explode("-",$fecha);
            $fecha = $fecha[2]."/".$fecha[1]."/".$fecha[0];
            self::json(['response'=>$Dia." , ".self::getFechaText($fecha)." - Hoy"]);
         }
         else
         {
            $fecha = explode("-",$fecha);
            $fecha = $fecha[2]."/".$fecha[1]."/".$fecha[0];
            self::json(['response'=>$Dia." , ".self::getFechaText($fecha)]);
         }
      }
    }

    /**
     * Mostrar pacientes que sacaraon la cita en una fecha - mostrar por médico y fecha
     */
    public static function PacientesTriajePersonalizado($fecha)
    {
        self::NoAuth();

        if(self::ValidateToken(self::get("token_")))
        {
            $model= new CitaMedica;
            if(self::profile()->rol === 'Médico')
            {
                $MedicoIdData = self::MedicoData()->id_medico;
                $Pacientes = $model->query()->Join("paciente as pc","ctm.id_paciente","=","pc.id_paciente")
                ->Join("persona as p","pc.id_persona","=","p.id_persona")
                ->select("concat(p.apellidos,' ',p.nombres) as paciente","pc.id_paciente","ctm.hora_cita","observacion","ctm.estado",
                "ctm.fecha_cita","ctm.id_cita_medica","ctm.id_horario")
                ->Where("ctm.fecha_cita","=",$fecha)
                ->And("ctm.id_medico","=",$MedicoIdData)
                ->InWhere("ctm.estado",["'pagado'","'pendiente'"])
                ->orderBy("ctm.hora_cita","asc")
                ->get();
            }else{
                $Pacientes = $model->query()->Join("paciente as pc","ctm.id_paciente","=","pc.id_paciente")
                ->Join("persona as p","pc.id_persona","=","p.id_persona")
                ->select("concat(p.apellidos,' ',p.nombres) as paciente","pc.id_paciente","ctm.hora_cita","observacion","ctm.estado",
                "ctm.fecha_cita","ctm.id_cita_medica")
                ->Where("ctm.fecha_cita","=",$fecha)
                ->InWhere("ctm.estado",["'pagado'","'pendiente'"])
                ->orderBy("ctm.hora_cita","asc")
                ->get();
            }

            self::json(['pacientes'=>$Pacientes]); 
        }
    }

    public static function pruebas()
    {
        $model = new CitaMedica;

        print_r(
       $model->query()->Join("paciente as pc","ctm.id_paciente","=","pc.id_paciente")
        ->Join("persona as p","pc.id_persona","=","p.id_persona")
        ->select("concat(p.apellidos,' ',p.nombres) as paciente","pc.id_paciente","ctm.hora_cita","observacion")
        ->Where("ctm.fecha_cita","=","2023-11-19")
        ->InWhere("ctm.estado",["'pagado'","'pendiente'"])
        ->get());
    }


}