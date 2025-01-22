<?php
namespace Http\controllers;
use lib\BaseController;
use models\CitaMedica;
use models\Detalle_Recibo;
use models\Recibo;

class ReciboController extends BaseController
{

    /** Mostramos los pacientes para generarles un recibo */
    public static function Pacientes_Sin_Recibo()
    {
        /// verificamos que estee authenticado
        self::NoAuth();/// redirige al login sin no esta Authenticado
        /// verificamos que el perfil sea médico
        if(self::profile()->rol === self::$profile[3])
        {
         /// validamos el token
         if(self::ValidateToken(self::get("token_")))
         {
            $Medico_Id = self::MedicoData()->id_medico;
            /// llamamos al procedimiento almacenado
            $model = new CitaMedica;

            $respuesta = $model->procedure("proc_pacientes_para_recibo","c",[$Medico_Id]);

            // imprimos en formato json
            self::json(["response" => $respuesta]);
         }
         else{
            self::json(["response" => "token-invalidate"]);
         }
        }
        else{
            self::json(["response"=>"no-authorized"]);
        }
    }

    /** Agregar lo servicios al detalle del recibo */
    public static function addDetalleService(){
     // verificamos que estee authenticado
     self::NoAuth();/// redirige al login sin no esta Authenticado
     /// verificamos que el perfil sea médico
     if(self::profile()->rol === self::$profile[3])
     {
      /// validamos el token
      if(self::ValidateToken(self::post("token_")))
      {
         /// añadimos a la cesta de detalle service
         self::addServiceCesta();
      }
      else{
         self::json(["response" => "token-invalidate"]);
      }
     }
     else{
         self::json(["response"=>"no-authorized"]);
     }   
    }


    /// proceso de añadir a la cesta los servicios
    private static function addServiceCesta()
    {
        if(!self::ExistSession("service_detalle"))
        {
            self::Session("service_detalle",[]);
        }

        /// verificamos si existe ese sertvicio

        if(!array_key_exists(self::post("service"),$_SESSION["service_detalle"]))
        {
            /// si no existe , añadimos a la cesta 
            $_SESSION["detalle_service"][self::post("service")]["servicio"] = self::post("service");
            $_SESSION["detalle_service"][self::post("service")]["precio"] = self::post("precio");
            $_SESSION["detalle_service"][self::post("service")]["preciomedico"] = self::post("preciomedico");
            $_SESSION["detalle_service"][self::post("service")]["precioclinica"] = self::post("precioclinica");
            $_SESSION["detalle_service"][self::post("service")]["cantidad"] = 1;
            $_SESSION["detalle_service"][self::post("service")]["service_id"] = self::post("id_serv");


            self::json(["response" => "add_ok"]);
        }
        else{
            self::json(["response" => "error_add"]);
        } 
    }

    /** MOSTRAR LO QUE CONTIENE LA CESTA DDEL DETALLE */
    public static function mostrarCestaServiceDetalle()
    {
     /// verificamos que estee authenticado
     self::NoAuth();/// redirige al login sin no esta Authenticado
     /// verificamos que el perfil sea médico
     if(self::profile()->rol === self::$profile[3])
     {
      /// validamos el token
      if(self::ValidateToken(self::get("token_")))
      {
         if(self::ExistSession("detalle_service"))
         {
            self::json(["response" => self::getSession("detalle_service")]);
         }
         else{
            self::json(["response"=>'vacio']);
         }
      }
      else{
         self::json(["response" => "token-invalidate"]);
      }
     }
     else{
         self::json(["response"=>"no-authorized"]);
     }   
    }
    
    /** Quitar servicios del carrito detalle */
    public static function QuitarServiceCart()
    {
    /// Verificamos que si no está authenticado, redirigimos al login
    self::NoAuth();
    /// verificamos que sea el médico quién realice esa acción
     if(self::profile()->rol === self::$profile[3])
     {
        /// validamos el token
        if(self::ValidateToken(self::post("token_")))
        {
            /// verificamos que exista la jey detalle_service
            if(isset($_SESSION["detalle_service"][self::post("service")]))
            {
                /// eliminamos el servicio del carrito
                 unset($_SESSION['detalle_service'][self::post("service")]);
                 self::json(["response" => "eliminado"]);
                 exit;
            }
        }else{
            self::json(["response" => "token-invalidate"]);
        }
     }
     else{
        self::json(["response" => "no-authorized"]);
     }
  }

  /** Registramos el recibo generado del paciente */
  public static function saveRecibo()
  {
     /// Verificamos que si no está authenticado, redirigimos al login
     self::NoAuth();
     /// verificamos que sea el médico quién realice esa acción
      if(self::profile()->rol === self::$profile[3])
      {
         /// validamos el token
         if(self::ValidateToken(self::post("token_")))
         {
            $recibo = new Recibo;

            $respuesta = $recibo->Insert([
                "numero_recibo" => self::post("recibo_numero"),
                "fecha_recibo" => self::FechaActual("Y-m-d H:i:s"),
                "monto_pagar" => self::post("monto"),
                "cita_id" => self::post("citaid")
            ]);

            if($respuesta)
            {
                $detalle_recibo = new Detalle_Recibo; $importe_ = 0.00;$importeMedico = 0.00;
                $importeClinica = 0.00;

                if(self::ExistSession("detalle_service"))
                {
                   foreach(self::getSession("detalle_service") as $detalle)
                   {
                    $importe_ = $detalle["precio"] * $detalle["cantidad"];
                    $importeMedico = $detalle["preciomedico"]* $detalle["cantidad"];
                    $importeClinica = $detalle["precioclinica"]* $detalle["cantidad"];

                    $value = $detalle_recibo->Insert([
                        "servicio" => $detalle["servicio"],
                        "precio" => $detalle["precio"],
                        "cantidad" => $detalle["cantidad"], 
                        "importe" => $importe_,
                        "service_id" =>$detalle["service_id"],
                        "recibo_id" => $recibo->ObtenerMaxRecibo()->num 
                    ]);
                   } 

                    if($value)
                    {
                        /// actualizamos la cita médica del atributo recibo_generado en si
                        $citamedica = new CitaMedica;
                        $citamedica->Update([
                            "id_cita_medica" => self::post("citaid"),
                            "monto_pago" => self::post("monto"),
                            "monto_medico" => $importeMedico,
                            "monto_clinica" => $importeClinica,
                            "recibo_generado" => "si"
                        ]);
                        self::destroySession("detalle_service");
                        self::json(["response" => "ok"]);
                    }
                    else
                    {
                        /// eliminamos el recibo registrado
                        $recibo->delete($recibo->ObtenerMaxRecibo()->num);
                        self::json(["response" => "erroradrian"]);
                    }
                }else
                {
                 /// eliminamos el recibo registrado
                 $recibo->delete($recibo->ObtenerMaxRecibo()->num);
                 self::json(["response" => "error"]);  
             
                }
            }
            else
            {
                self::json(["response" => "error"]);
            }
         }
    }
  }

  /// Método que cancelar para generar el recibo del paciente
  public static function CancelRecibo(int $id)
  {
    /// Verificamos que si no está authenticado, redirigimos al login
    self::NoAuth();
    /// verificamos que sea el médico quién realice esa acción
     if(self::profile()->rol === self::$profile[3])
     {
        /// validamos el token
        if(self::ValidateToken(self::post("token_")))
        {
           $modelCita = new CitaMedica;
           
           $modelCita->Update([
            "id_cita_medica" => $id,
            "recibo_generado" => null
           ]);
           self::json(["response" => "ok"]);
        }else
        {
            self::json(["response" => "token-invalidate"]);
        }
    }else{
        self::json(["response" => "no-authorized"]);
    }
  }

  /// limpiar lo añadido en la cesta , para casos de que el usuario desea cancelar
  public static function cancelDataRecibo()
  {
     /// Verificamos que si no está authenticado, redirigimos al login
     self::NoAuth();
     /// verificamos que sea el médico quién realice esa acción
      if(self::profile()->rol === self::$profile[3])
      {
         /// validamos el token
         if(self::ValidateToken(self::post("token_")))
         {
            if(self::ExistSession("detalle_service"))
            {
                self::destroySession("detalle_service");
                self::json(["response" => 'ok']);
            }
         }else
         {
            self::json(["response" => "token-invalidate"]);
         }
      }
      else
      {
        self::json(["response" => "no-authorized"]);
      }
  }
  /**mostramos los recibos que ya han sido generados */
  public static function mostrar_recibos_generados()
  {
     /// Verificamos que si no está authenticado, redirigimos al login
     self::NoAuth();
     /// verificamos que sea el médico quién realice esa acción
      if(self::profile()->rol === self::$profile[3])
      {
         /// validamos el token
         if(self::ValidateToken(self::get("token_")))
         { 
          $model_recibo = new Recibo;

          $MedicoIdAuth_ = self::MedicoData()->id_medico;
          $data = $model_recibo->query()
          ->Join("cita_medica as cm","re.cita_id","=","cm.id_cita_medica")
          ->Join("medico as m","cm.id_medico","=","m.id_medico")
          ->Join("paciente as pc","cm.id_paciente","=","pc.id_paciente")
          ->Join("persona as p","pc.id_persona","=","p.id_persona")
          ->select("re.id_recibo","re.numero_recibo","re.fecha_recibo","concat(p.apellidos,' ',p.nombres) as paciente_",
          "re.monto_pagar")
          ->Where("cm.id_medico","=",$MedicoIdAuth_)
          ->get();
          self::json(["response" => $data]);
         }
         else
         {
          self::json(["response" => "token-invalidate"]);
         }
     }else
     {
        self::json(["response" => "no-authorized"]);
     }
  }
}