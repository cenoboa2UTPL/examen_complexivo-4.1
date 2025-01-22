<?php 
namespace Http\controllers;
use FPDF;
use Http\pageextras\PageExtra;
use lib\BaseController;
use models\Detalle_Receta_Electronico;
use models\Empresa;
use models\Paciente;
use models\Plan_Atencion;
use models\ProductoFarmacia;
use models\Receta;
use models\RecetaElectronico;

class RecetaController extends BaseController
{
    /// generar reporte 
    private static $Model;
    public static function informe_receta_medica()
    {
        self::NoAuth();
        
       if(isset($_GET['v']) and self::profile()->rol === self::$profile[3])
       {
        self::$Model = new Plan_Atencion;
        $RecetaDetalle = self::$Model->procedure("proc_receta_show","C",[self::get("v")]);
 
        if(count($RecetaDetalle) > 0)
        {
            $empresa = new Empresa;
            $DataEmpresa = $empresa->query()
            ->limit(1)
           ->first();
            $receta = new FPDF();
            $receta->SetTitle("Receta médica - ".$RecetaDetalle[0]->paciente,1);
            $receta->AddPage();/// Añadimos una nueva página
            $receta->SetY(10);
            $receta->SetX(40);
            $receta->SetDrawColor(112, 128, 144);
            $receta->SetFillColor(240, 248, 255);
            $receta->SetTextColor(0,0,0);
            $receta->SetFont("Arial","B",12);
            $receta->Cell(118,10,utf8__('Receta médica'),1,1,'C',1);
            $receta->SetY(20);
            $receta->SetX(40);
            $receta->SetFont("Arial","",10);
            $receta->Cell(35,23,$receta->Image(isset($DataEmpresa->logo)?"public/asset/empresa/".$DataEmpresa->logo:"public/asset/img/lgo_clinica_default.jpg" ,null,20,35,23,'PNG'),1,0,'C');
            $receta->SetFont("Arial","B",10);
            $receta->SetTextColor(0,0,0);
            $receta->Cell(53,23,utf8__(isset($DataEmpresa->nombre_empresa) ?$DataEmpresa->nombre_empresa:'XXXXXXXXXXXXXXX'),1,1,'C');
            $receta->SetFont("Arial","",13);
            $receta->SetY(20);
            $receta->SetX(128);
            $receta->Cell(30,23,str_replace("-","/",self::FechaActual("d-m-Y")),1,1,'C');
    
            $receta->SetFont("Arial","B",10);
            $receta->SetY(43);
    
    
            $receta->SetX(40);
            $receta->Cell(35,7,'Especialista',1,0,'L');
            $receta->SetFont("Arial","",10);
            $receta->Cell(83,7,utf8__($RecetaDetalle[0]->medico_atencion),1,1,'L');
            $receta->SetX(40);
            $receta->SetTextColor(0,0,0);
            $receta->SetFont("Arial","B",10);
            $receta->Cell(35,7,utf8__('Paciente'),1,0,'L');
            $receta->SetFont('Arial','',10);
            $receta->Cell(83,7,utf8__($RecetaDetalle[0]->paciente),1,1,'L');
            $receta->SetX(40);
            $receta->SetFont("Arial","B",10);
            $receta->Cell(35,7,utf8__('Fecha atención'),1,0,'L');
            $receta->SetFont("Arial","",10);
            $receta->Cell(83,7,self::getFechaText($RecetaDetalle[0]->fecha_atencion).'          '.$RecetaDetalle[0]->horacita,1,1,'L');
            $receta->SetFont("Arial","B",10);
            $receta->SetX(40);
            $receta->SetFont("Arial",'B',10);
            $receta->Cell(118,7,'Tratamiento',1,1,'L');
            $receta->SetX(40);
            $receta->SetFont('Arial','',10);
            $receta->MultiCell(118,7,$RecetaDetalle[0]->desc_plan != null ? utf8__(str_replace("\n"," - ",$RecetaDetalle[0]->desc_plan)):'----------------------------',1);
            $receta->SetFont("Arial","B",10);
            $receta->SetX(40);
            $receta->Cell(35,7,'Plan de tratamiento ',1,0,'L');
            $receta->SetFont("Arial","",10);
            $receta->SetFillColor(105, 105, 105);
            $receta->Cell(83,7,utf8__($RecetaDetalle[0]->plan_tratamiento),1,1,'L');
            $receta->SetFont("Arial","B",10);
            $receta->SetX(40);
            $receta->SetFont("Arial","B",10);
            $receta->Cell(118,7,utf8__('Descripción de los exámenes'),1,1,'L');
            $receta->SetX(40);
            $receta->SetFont("Arial","",8);
            $receta->MultiCell(118,7,utf8__($RecetaDetalle[0]->desc_analisis_requerida == null?'----------------------------------------------------------------------------':$RecetaDetalle[0]->desc_analisis_requerida),1);

            $receta->SetX(40);
    
            $receta->SetFont("Arial","B",10);
            $receta->Cell(118,7,utf8__("Próxima cita"),1,0,"L");

            $receta->SetX(70);
            $receta->SetFont("Arial","",10);
            $receta->Cell(88,7,self::getFechaText($RecetaDetalle[0]->proxima_cita_medica),1);

            $receta->Ln();
            $receta->SetX(40);
            $receta->SetFont("Arial","B",10);
            $receta->Cell(118,7,'Productos recetados',1,1,'L');
            $receta->SetFont("Arial","I",7);
            $receta->SetX(40);
    
            $recetaDet = '';
            
            foreach($RecetaDetalle as $rec)
            {
               $recetaDet.='* '.$rec->medicamento.PHP_EOL.mb_convert_encoding($rec->dosis, 'UTF-8', 'UTF-8').PHP_EOL.'Cantidad : '.$rec->cantidad.PHP_EOL;
            }
            $receta->MultiCell(118,5,utf8__($recetaDet).
            '---------------------------------------------------------------------------------------------------------------------------------------------'.PHP_EOL.'TIEMPO TRATAMIENTO : '.utf8__($RecetaDetalle[0]->plan_tratamiento).PHP_EOL,1);
            // Arial italic 
            $receta->Ln(30);
            $receta->SetFont('Arial','B',10);
            $receta->SetDrawColor(105, 105, 105);
            $receta->Cell(0,10,'_____________________________________',0,1,'C');
            $receta->SetFont("Arial","B",10);
            $receta->Cell(0,0,utf8__("Firma Dr. ".self::profile()->apellidos."    ".self::profile()->nombres),0,1,'C');
            
    #'D', "Receta médica - ".$RecetaDetalle[0]->paciente.".pdf", true 
            $receta->Output();/// mostramos el pdf
        }
        else
        {
            PageExtra::PageNoAutorizado();
        }
       }
       else
       {
        PageExtra::PageNoAutorizado();
       }
    }

    /**
     * Otro informe de la receta (personalizado)
     */
    public static function informe_receta_medica_personalizado()
    {
       
        self::NoAuth();
        
       if(isset($_GET['v']) and self::profile()->rol === self::$profile[3])
       {
        self::$Model = new RecetaElectronico;
        $RecetaDetalle = self::$Model->query()
        ->Join("detalle_receta_electronico as dre","dre.id_receta_electro","=","rel.id_receta_electro")
        ->where("serie_receta","=",$_GET["v"])
        ->get();
 
        if(count($RecetaDetalle) > 0)
        {
            $empresa = new Empresa;
            $DataEmpresa = $empresa->query()
            ->limit(1)
           ->first();
            $receta = new FPDF("P","mm",array(175,200));
            $receta->SetTitle("Receta médica - ".$RecetaDetalle[0]->pacientedata,1);
            $receta->AddPage();/// Añadimos una nueva página
            $receta->SetY(10);
            $receta->SetX(20);
            $receta->SetDrawColor(112, 128, 144);
            $receta->SetFillColor(240, 248, 255);
            $receta->SetTextColor(0,0,0);
            $receta->SetFont("Arial","B",12);
            $receta->Cell(138,10,utf8__('Receta médica'),1,1,'C',1);
            $receta->SetY(20);
            $receta->SetX(20);
            $receta->SetFont("Arial","",10);
            $receta->Cell(55,23,$receta->Image(isset($DataEmpresa->logo)?"public/asset/empresa/".$DataEmpresa->logo:"public/asset/img/lgo_clinica_default.jpg" ,null,20,35,23,'PNG'),1,0,'C');
            $receta->SetFont("Arial","B",10);
            $receta->SetTextColor(0,0,0);
            $receta->Cell(53,23,utf8__(isset($DataEmpresa->nombre_empresa) ?$DataEmpresa->nombre_empresa:'XXXXXXXXXXXXXXX'),1,1,'C');
            $receta->SetFont("Arial","",13);
            $receta->SetY(20);
            $receta->SetX(128);
            $receta->Cell(30,23,str_replace("-","/",self::FechaActual("d-m-Y")),1,1,'C');
    
            $receta->SetFont("Arial","B",10);
            //$receta->SetY(43);
    
    
            $receta->SetX(20);
            $receta->Cell(55,7,'Especialista',1,0,'L');
            $receta->SetFont("Arial","",10);
            $receta->Cell(83,7,utf8__(self::profile()->apellidos." ".self::profile()->nombres),1,1,'L');
            $receta->SetX(20);
            $receta->SetTextColor(0,0,0);
            $receta->SetFont("Arial","B",10);
            $receta->Cell(55,7,utf8__('Paciente'),1,0,'L');
            $receta->SetFont('Arial','',10);
            $receta->Cell(83,7,utf8__($RecetaDetalle[0]->pacientedata),1,1,'L');
            // $receta->SetX(40);
            // $receta->SetFont("Arial","B",10);
            // $receta->Cell(35,7,utf8__('Fecha atención'),1,0,'L');
            // $receta->SetFont("Arial","",10);
            // $receta->Cell(83,7,self::getFechaText($RecetaDetalle[0]->fecha_atencion).'          '.$RecetaDetalle[0]->horacita,1,1,'L');
            // $receta->SetFont("Arial","B",10);
            // $receta->SetX(40);
            // $receta->SetFont("Arial",'B',10);
            // $receta->Cell(118,7,'Tratamiento',1,1,'L');
            // $receta->SetX(40);
            // $receta->SetFont('Arial','',10);
            // $receta->MultiCell(118,7,$RecetaDetalle[0]->desc_plan != null ? utf8__(str_replace("\n"," - ",$RecetaDetalle[0]->desc_plan)):'----------------------------',1);
            // $receta->SetFont("Arial","B",10);
            // $receta->SetX(40);
            // $receta->Cell(35,7,'Plan de tratamiento ',1,0,'L');
            // $receta->SetFont("Arial","",10);
            // $receta->SetFillColor(105, 105, 105);
            // $receta->Cell(83,7,utf8__($RecetaDetalle[0]->plan_tratamiento),1,1,'L');
            // $receta->SetFont("Arial","B",10);
            // $receta->SetX(40);
            // $receta->SetFont("Arial","B",10);
            // $receta->Cell(118,7,utf8__('Descripción de los exámenes'),1,1,'L');
            // $receta->SetX(40);
            // $receta->SetFont("Arial","",8);
            // $receta->MultiCell(118,7,utf8__($RecetaDetalle[0]->desc_analisis_requerida == null?'----------------------------------------------------------------------------':$RecetaDetalle[0]->desc_analisis_requerida),1);

            // $receta->SetX(40);
    
            // $receta->SetFont("Arial","B",10);
            // $receta->Cell(118,7,utf8__("Próxima cita"),1,0,"L");

            // $receta->SetX(70);
            // $receta->SetFont("Arial","",10);
            // $receta->Cell(88,7,self::getFechaText($RecetaDetalle[0]->proxima_cita_medica),1);

            //$receta->Ln();
            $receta->SetX(20);
            $receta->SetFont("Arial","B",10);
            $receta->Cell(138,7,'Productos recetados',1,1,'L');
            $receta->SetFont("Arial","I",7);
            $receta->SetX(20);
    
            $recetaDet = '';
            
            foreach($RecetaDetalle as $rec)
            {
               $recetaDet.='* '.$rec->productodata.PHP_EOL.mb_convert_encoding($rec->frecuencia, 'UTF-8', 'UTF-8').PHP_EOL.'Cantidad : '.$rec->cantidad_producto.PHP_EOL;
            }
            $receta->MultiCell(138,5,utf8__($recetaDet).
            '---------------------------------------------------------------------------------------------------------------------------------------------------------------'.PHP_EOL.'TIEMPO TRATAMIENTO : '.utf8__($RecetaDetalle[0]->tiempo_tratamiento).PHP_EOL,1);
            // Arial italic 
            $receta->Ln(30);
            $receta->SetFont('Arial','B',10);
            $receta->SetDrawColor(105, 105, 105);
            $receta->Cell(0,10,'_____________________________________',0,1,'C');
            $receta->SetFont("Arial","B",10);
            $receta->Cell(0,0,utf8__("Firma Dr. ".self::profile()->apellidos."    ".self::profile()->nombres),0,1,'C');
            
    #'D', "Receta médica - ".$RecetaDetalle[0]->paciente.".pdf", true 
            $receta->Output();/// mostramos el pdf
        }
        else
        {
            PageExtra::PageNoAutorizado();
        }
       }
       else
       {
        PageExtra::PageNoAutorizado();
       }
    }
    /**
     * Vista para generar una receta médica
     */
    public static function generarRecetaView()
    {
        self::NoAuth();
        if(self::profile()->rol === self::$profile[3])
        {
            self::View_("medico.generar_receta");
        }else{
            PageExtra::PageNoAutorizado();
        }
    }

    public static function buscarPaciente()
    {
        self::NoAuth();
        $datos = [];
        if(self::profile()->rol === self::$profile[3])
        {
            $modelPaciente = new Paciente;

            $datosPaciente = $modelPaciente->query()
                             ->Join("persona as per","pc.id_persona","=","per.id_persona")
                             ->get();
            $datos = ["pacientes" =>$datosPaciente];    
        }else{
             $datos = ["pacientes" => []];
        }

        self::json($datos);
    } 

    /// buscar los productos de la clinica para recetar al paciente
    public static function buscarProductos()
    {
        self::NoAuth();
        $datos = [];
        if(self::profile()->rol === self::$profile[3])
        {
            $modelProductos = new ProductoFarmacia;

            $datosProductos = $modelProductos->mostrar(""); 
            $datos=["productos" => $datosProductos];
        }else{
             $datos = ["pacientes" => []];
        }

        self::json($datos);  
    }

    /// añadir a la cesta de la receta
    public static function anadirReceta()
    {
       self::NoAuth();

       if(self::profile()->rol === self::$profile[3])
       {
           if(self::ValidateToken(self::post("token_"))){
                /// verificamos si existe la sesion
                if (!isset($_SESSION["receta"])) {
                    $_SESSION["receta"] = [];
                }

                /// verificamos si la key existe, osea si el producto agregado existe
                if (!array_key_exists(self::post("producto"), $_SESSION["receta"])) {
                    $_SESSION["receta"][self::post("producto")]["producto"] = self::post("producto");
                    $_SESSION["receta"][self::post("producto")]["frecuencia"] = self::post("frecuencia");
                    $_SESSION["receta"][self::post("producto")]["dosis"] = self::post("dosis");
                    $_SESSION["receta"][self::post("producto")]["cantidad"] = self::post("cantidad");
                   
                    self::json(["response" => "agregado"]);
                }else{
                    self::json(["response" => "existe"]);
                }
           }else{
            self::json(["errortoken" => "Token invalid!"]);
           }
       }else{
        self::json(["erroracceso" => "No tienes authorizado esta tárea"]);
       }
    }

    /// mostrar los productos de la receta
    public static function showRecetaData()
    {
        self::NoAuth();
        if(self::profile()->rol === self::$profile[3])
        {
            if(self::ExistSession("receta")){
                $recetaDetalle = self::getSession("receta");

                self::json(["receta"=>$recetaDetalle]);
            }else{
                self::json(["receta"=>[]]);
            }
        }
    }

    /**
     * Eliminar un detalle de la receta seleccionado
     */
    public static function eliminarDetalleSeleccionado()
    {
        self::NoAuth();
        if(self::profile()->rol === self::$profile[3])
        {
            if(self::ValidateToken(self::post("token_")))
            {
                if(self::ExistSession("receta")){
                    unset($_SESSION["receta"][self::post("producto")]);
   
                   self::json(["response"=>"eliminado"]);
               }
            }else{
                self::json(["error_token"=>"Token Csrf invalid!"]);
            }
        } 
    }

    /**
     * Guardar la receta médica del paciente
     */
    public static function SaveReceta(){
        self::NoAuth();

        if(self::profile()->rol === self::$profile[3])
        {
            if(self::ValidateToken(self::post("token_")))
            {
                $modelReceta = new RecetaElectronico;

                $NumRecibo = date("YmdHis");

                $receta = $modelReceta->Insert([
                   "fecha_receta" => self::FechaActual("Y:m:d H:i:s"),
                   "serie_receta" => $NumRecibo,
                   "id_paciente" => self::post("paciente_id"),
                   "pacientedata" => self::post("paciente_receta"),
                   "tiempo_tratamiento" => self::post("tiempo_tratamiento"),
                   "id_medico" => self::MedicoData()->id_medico
                ]);

                /** Registramos en la tabla de receta electronica */
                if(self::ExistSession("receta") and $receta == true){

                    /// obtenemos la receta electronico registrado
                    $RecetaElectro = $modelReceta->query()->where("serie_receta","=",$NumRecibo)->first();
                    
                    $modelDetalleReceta = new Detalle_Receta_Electronico;
                    foreach(self::getSession("receta") as $receta)
                    {
                        $respuesta = $modelDetalleReceta->Insert([
                           "id_receta_electro" => $RecetaElectro->id_receta_electro,
                            "productodata" => $receta["producto"],
                            "frecuencia" => $receta["frecuencia"],
                            "tiempo" => $receta["dosis"],
                            "cantidad_producto" => $receta["cantidad"],
                          ]);
                    }
                    if($respuesta)
                    {
                        /// obtener el id de la receta 
                        self::json(["response" => "ok-".$NumRecibo]);
                        //url = "receta_medica?v=".$NumRecibo;

                        //echo "<script>window.open(".$url.",'_blank')</script>";
                    } 
                    else{
                        self::json(["response" => "error"]);
                    }
                }else{
                    self::json(["response" => "errorsession"]);
                }
            }
        }
    }

    /**
     * Cancelar o eliminar todo de la cesta carrito
     */
    public static function eliminarDeLaCestaReceta()
    {
        self::NoAuth();
       if(self::profile()->rol === self::$profile[3])
        {
            if(self::ValidateToken(self::post("token_")))
            {
               if(self::ExistSession("receta"))
               {
                  unset($_SESSION["receta"]);  
                  self::json(["response" => "ok"]);
               }
            }
        }
    }

    /**
     * Ver las recetas electronicas generados
     */
    public static function showRecetasGenerados(){
        self::NoAuth();

        if(self::profile()->rol === "Médico"){
            $modelreceta = new RecetaElectronico;

            $responseReceta = $modelreceta->query()->get();

            self::json(["recetas"=>$responseReceta]);
        }else{
            self::json(["resetas"=>[]]);
        }
    }

    /**
     * Eliminar la receta
     */
    public static function deleteRecetaElectronica($id){
        self::NoAuth();
        if(self::profile()->rol === "Médico"){
            if(self::ValidateToken(self::post("token_"))){
                $modelreceta = new RecetaElectronico;

                $response = $modelreceta->delete($id);

                self::json(["response"=>$response?'ok':'error']);
            }else{
                self::json(["response"=>"token-invalid"]);
            }
        }else{
            self::json(["response"=>"no-authorized"]);
        }
    }
}