<?php 
namespace Http\controllers;

use Http\pageextras\PageExtra;
use lib\BaseController;
use models\Especialidad;
use models\Especialidad_Medico;
use models\Servicio;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ServicioController extends BaseController
{
    private static string  $TipoArchivoAceptable = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    /**
     * Mostrar la vista de servicios
     */
    public static function index()
    {
        self::NoAuth();
        if(self::profile()->rol === self::$profile[0]){
            $modelespecialidad = new Especialidad;
            $especialidades = $modelespecialidad->query()->get();
            self::View_("medico.servicio",compact("especialidades"));
        }else{
          PageExtra::PageNoAutorizado();
        }
    }

    /// mostrar a todos los médicos de una especialidad
    public static function showMedicosPorEspecialidad($id){
        self::NoAuth();
        if(self::profile()->rol === self::$profile[0]){
          $modelmedicoesp = new  Especialidad_Medico;

          $medicos= $modelmedicoesp->query()->Join("especialidad as e","med_esp.id_especialidad","=","e.id_especialidad")
                                     ->Join("medico as m","med_esp.id_medico","=","m.id_medico")
                                     ->Join("persona as p","m.id_persona","=","p.id_persona")
                                     ->where("med_esp.id_especialidad","=",$id)
                                     ->select("med_esp.id_medico_esp","med_esp.id_medico","p.apellidos","p.nombres")
                                     ->get();
            self::json(["medicos"=>$medicos]);
        }else{
            self::json(["medicos"=>[]]);
        }  
    }

    /**Mostrar todos los servicios de un médico con respecto a una especialidad */
    public static function showServicesMedico($id){
        self::NoAuth();
        if(self::profile()->rol === self::$profile[0]){
          $modelservicio= new  Servicio;

          $servicios= $modelservicio->query()->where("id_medico_esp","=",$id)->get();
            self::json(["servicios"=>$servicios]);
        }else{
            self::json(["servicios"=>[]]);
        }  
    }

    /// Actualizar datos del servicio
    public static function update($id){
        self::NoAuth();

        if(self::ValidateToken(self::post("token_"))){
            $serviceModel = new Servicio;
            $response = $serviceModel->Update([
                "id_servicio" => $id,
                "name_servicio" => self::post("name_servicio"),
                "precio_servicio" => self::post("precio_servicio"),
                "precio_medico" => self::post("precio_medico"),
                "precio_clinica" => self::post("precio_clinica")
            ]);

            self::json(["response" => $response ? 'ok':'error']);
        }else{
            self::json(["response" => "token-invalidate"]);
        }
    }

    /// Guardar nuevo servicio
    /// Actualizar datos del servicio
    public static function store(){
        self::NoAuth();

        if(self::ValidateToken(self::post("token_"))){
            $serviceModel = new Servicio;
            $response = $serviceModel->Insert([
                "name_servicio" => self::post("name_servicio"),
                "precio_servicio" => self::post("precio_servicio"),
                "id_medico_esp"=>self::post("medico_esp"),
                "precio_medico" => self::post("precio_medico"),
                "precio_clinica" => self::post("precio_clinica")
            ]);

            self::json(["response" => $response ? 'ok':'error']);
        }else{
            self::json(["response" => "token-invalidate"]);
        }
    }

    /**
     * Importar con el archivo excel
     */
    public static function importarServicios(){
        if(self::ValidateToken(self::post("token_")))
        {
          /// validamos de que exista el archivo seleccionado
          if(self::file_size("file_excel") > 0)
          {
               /// Ahora validamos que sea un archivo excel
              if(self::file_Type("file_excel") === self::$TipoArchivoAceptable)
              {
               /// realizamos el import data del servicio
               self::importarServicioExcelMedico(self::ContentFile("file_excel"));
              }else
              {
                self::json(["response" => "no-accept"]);
              }
          }
          else
          {
            self::json(["response"=>"vacio"]);
          }
        }
    }


      /// proceso importar datos del servicio excel al la tabla servicio
  private static function importarServicioExcelMedico($archivo)
  {
   $modelService = new Servicio;
   /// llamar a la libreria office
   $office = IOFactory::load($archivo);

   /// indicamos la hoja 0 
   $HojaCero = $office->getSheet(0);

   /// indicamos la cantidad de filas que tiene esa hoja 0
   $RowsHoja = $HojaCero->getHighestDataRow();


   for($fila_row = 2;$fila_row  <= $RowsHoja;$fila_row++ )
   {
     $NombreServicio = $HojaCero->getCellByColumnAndRow(1,$fila_row);

     $PrecioServicio = $HojaCero->getCellByColumnAndRow(2,$fila_row);

     $PrecioMedicoService = $HojaCero->getCellByColumnAndRow(3,$fila_row);
     $PrecioClinicaService = $HojaCero->getCellByColumnAndRow(4,$fila_row);

     // agregamos a la tabla servicio
     if(self::existeServicio($NombreServicio, self::post("medico_esp")))
     {
      $Response = 'existe';
     }
     else
     {
      $Response = $modelService->Insert([
        "name_servicio" => $NombreServicio,
        "precio_servicio" => $PrecioServicio,
        "precio_medico"=>$PrecioMedicoService,
        "precio_clinica" => $PrecioClinicaService,
        "id_medico_esp" => self::post("medico_esp")
       ]);
     }
   }

   self::json(['response' => $Response?'ok':($Response === 'existe'?'existe':'error')]);
  }

  /**
   * Verificamos si existe ya el servicio
   */
  private static function existeServicio(string $servicio,int $medico_esp)
  {
    $modelService = new Servicio;

    return $modelService->query()->Where("name_servicio","=",$servicio)
    ->And("id_medico_esp","=",$medico_esp)->first();
  }
}