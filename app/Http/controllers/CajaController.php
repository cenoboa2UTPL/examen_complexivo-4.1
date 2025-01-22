<?php
namespace Http\controllers;

use FPDF;
use Http\pageextras\PageExtra;
use lib\BaseController;
use lib\PdfResultados;
use models\Caja;
use models\CategoriaEgreso;
use models\CitaMedica;
use models\ComprasFarmacia;
use models\Empresa;
use models\VentasFarmacia;

class CajaController extends BaseController
{
  /// mostramos la vista para gestionar la caja

  public static function index()
  {
    self::NoAuth();
    if(self::profile()->rol === 'admin_general' || self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia')
    {
     self::View_("caja.index");
    }else{
        PageExtra::PageNoAutorizado();
    }
  }

  /// guardar la apertura de una caja
  public static function store()
  {
    self::NoAuth();
    if(self::profile()->rol === 'admin_general' ||self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia'){
      if(self::ValidateToken(self::post("_token")))
      {
       $modelCajaApertura = new Caja; $Input = "";

       /// verificamos que sea el director quién apertura la caja  
       if(self::profile()->rol === 'Director')
       {
        /// calculamos el saldo final 
        
        $respuesta = $modelCajaApertura->Insert([
          "fecha_apertura_clinica" => self::FechaActual("Y-m-d H:i:s"),
          "saldo_inicial_clinica" => self::post("monto_apertura"),
          "estado_clinica"=>"a"
        ]);

       }else{
        if(self::profile()->rol === 'Farmacia' || self::profile()->rol === 'admin_farmacia')
        {
         
         $respuesta = $modelCajaApertura->Insert([
          "fecha_apertura_farmacia" => self::FechaActual("Y-m-d H:i:s"),
          "saldo_inicial_farmacia" => self::post("monto_apertura"),
          "estado_farmacia" => "a"
          ]);
  
        } 
       }

       self::json(["response" =>$respuesta]);
      }else{
        self::json(["response" => "token-invalidate"]);
      }
    }else{
      self::json(["response" => "no-authorized"],403);
    }
  }

  /// mostrar la apertura d ecaja
  public static function mostrarAperturasCaja()
  {
    self::NoAuth();
    if(self::profile()->rol === 'admin_general' || self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia'){
       
       $modelCajaApertura = new Caja;

       $respuesta = $modelCajaApertura->query()->get();
    
        self::json(["response" =>$respuesta]);
    }else{
      self::json(["response" => "no-authorized"],403);
    }
  }

  /**
   * Eliminar la caja aperturada
   */
  public static function EliminarCajaApertura($id)
  {
    $modelcaja = new Caja;

    $response = $modelcaja->delete($id);

    if($response){
       self::json(["response" => "ok"]);
    }else{
      self::json(["response" => "error"]);
    }
  }

  /// CERRAR LA CAJA APERTURADA
  public static function CerrarCajaAperturada(int $id)
  {
    self::NoAuth();

    if(self::profile()->rol === 'admin_general' || self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia'){
      if(self::ValidateToken(self::post("_token")))
      {
       $modelCajaApertura = new Caja;$modelEgreso = new CategoriaEgreso; $Saldo_Final = 0.00;

       /// obtenemos la caja aperturada
       $CajaActual = $modelCajaApertura->query()->Where("estado_clinica","=","a")
       ->Or("estado_farmacia","=","a")
       ->limit(1)->first();

        /// obtenemos el total egresos
        $TotalEgreso = $modelEgreso->query()->Join("subcategorias_egreso as se","se.categoriaegreso_id","=","ce.id_categoria_egreso")
        ->select("sum(se.valor_gasto) as total_gasto")
        ->Where("ce.fecha_categoria","=",self::FechaActual("Y-m-d"))->first();
       
        /// calculamos el ingreso de la clínica
        $modelcita = new CitaMedica;
        /// obtenemos el ingreso total de la clínica al cerrar la caja
        $IngrestoTotalClinica = $modelcita->query()
        ->select("sum(monto_clinica) as total")
        ->Where("fecha_cita","=",self::FechaActual("Y-m-d"))
        ->And("estado","<>","anulado")->first();
        /// calculamos total de las ventas y compras
        $modelventas = new VentasFarmacia; $modelcompras = new ComprasFarmacia;
        /// obtenemos el ingreso total de la clínica al cerrar la caja
        $TotalVentas = $modelventas->query()
        ->select("sum(total_venta) as totalventas")
        ->Where("fecha_emision","=",self::FechaActual("Y-m-d"))
        ->first();

        $TotalCompras = $modelcompras->query()
        ->select("sum(total_compra) as totalcompras")
        ->Where("fecha_compra","=",self::FechaActual("Y-m-d"))
        ->first();
       /// verificamos que sea el director quién cerrará la caja primeramente
       if(self::profile()->rol === 'Director')
       {
        /// calculamos el saldo final 
        $Saldo_Final = ($CajaActual->saldo_inicial_clinica +$IngrestoTotalClinica->total);
        $respuesta = $modelCajaApertura->Update([
          "id_apertura_caja" => $id,
          "fecha_cierre_clinica" => self::FechaActual("Y-m-d H:i:s"),
          "ingreso_clinica" => $IngrestoTotalClinica->total == null ? 0.00 :$IngrestoTotalClinica->total,
          "saldo_final_clinica" => $Saldo_Final,
          "total_egreso" => $TotalEgreso->total_gasto,
          "estado_clinica" => "c"
        ]);
       }else{
        if(self::profile()->rol === 'Farmacia' || self::profile()->rol === 'admin_farmacia')
        {
        /// calculamos el saldo
        $Saldo_Final = ($CajaActual->saldo_inicial_farmacia +$TotalVentas->totalventas) - ($TotalCompras->totalcompras);
          $respuesta = $modelCajaApertura->Update([
            "id_apertura_caja" => $id,
            "fecha_cierre_farmacia" => self::FechaActual("Y-m-d H:i:s"),
            "total_ventas" => $TotalVentas->totalventas,
            "total_compras" => $TotalCompras->totalcompras,
            "saldo_final_farmacia" => $Saldo_Final,
            "total_egreso" => $TotalEgreso->total_gasto,
            "estado_farmacia" => "c"
          ]);
  
        } 
       }
       self::json(["response" => $respuesta]);
      }else{
        self::json(["token-invalidate"]);
      }
    }else{
      self::json(["response" => "no-authrized"]);
    }
  }

  /// ver el informe de cierre de caja
  public static function informeCierreCaja(int $id)
  {
    self::NoAuth();

    if(self::profile()->rol === 'admin_general' || self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia'){
     $informeCierreCaja = new FPDF("P","mm",array(180,140));

     $informeCierreCaja->SetTitle("Informe de cierre de caja");

     $informeCierreCaja->AddPage();

     /// agregamos datos de la empresa
     $empresa = new Empresa;
        $DataEmpresa = $empresa->query()
        ->limit(1)->first();

    if(!$DataEmpresa){PageExtra::PageNoAutorizado();}
    $informeCierreCaja->setFont("Times","B",15);
    $informeCierreCaja->Cell(90,0,$informeCierreCaja->Image(isset($DataEmpresa->logo)?"public/asset/empresa/".$DataEmpresa->logo:"public/asset/img/lgo_clinica_default.jpg",50.5,5,42,42,'PNG'),0,0);
    $informeCierreCaja->Ln(36);

    $informeCierreCaja->setFont("Times","B",12);
    $informeCierreCaja->MultiCell(135,5,utf8__(isset($DataEmpresa->nombre_empresa) ?$DataEmpresa->nombre_empresa:'XXXXXXXXXXXXXXX')." RUC : ".(isset($DataEmpresa->ruc)  ? $DataEmpresa->ruc:" RUC: xxxxxxxxxxx")." ".(isset($DataEmpresa->direccion) ? utf8__($DataEmpresa->direccion):'xxxxxxxxxxxxxxxxxxxxx'),0,"L");

    $informeCierreCaja->Ln(9);
    $informeCierreCaja->setFont("Times","B",13);
    $informeCierreCaja->Cell(120,2,self::profile()->rol === 'Director' ? utf8__("INFORME DE CIERRE DE CAJA- CLÍNICA"):"INFORME DE CIERRE DE CAJA- FARMACIA",0,1,"C");
    $informeCierreCaja->Cell(120,2,"_______________________________________________",0,1,"C");
    $informeCierreCaja->Ln(8);


    /// datos del cierre de caja

    $Cajamodel = new Caja;

    $dataCaja = $Cajamodel->query()->Where("id_apertura_caja","=",$id)
    ->first();

    if(!$dataCaja){PageExtra::PageNoAutorizado();}
    $SaldoInit = self::profile()->rol === 'Director' ? $dataCaja->saldo_inicial_clinica : $dataCaja->saldo_inicial_farmacia;
    $SaldoFin = self::profile()->rol === 'Director' ? $dataCaja->saldo_final_clinica : $dataCaja->saldo_final_farmacia;
         
    $informeCierreCaja->SetDrawColor(0, 0, 128);
    $informeCierreCaja->SetX(12);
    $informeCierreCaja->SetFont("Times","B",12);
    $informeCierreCaja->Cell(68,"6","Saldo inicial  entregado ",1,0,"L");
    $informeCierreCaja->SetFont("Times","",12);
    $informeCierreCaja->Cell(47,"6",(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda : 'S/.')." ".$SaldoInit,1,1,"R");

    /// ingresos clinica
     if(self::profile()->rol === 'Director' || self::profile()->rol === 'admin_general')
     {
      $informeCierreCaja->SetDrawColor(0, 0, 128);
      $informeCierreCaja->SetX(12);
      $informeCierreCaja->SetFont("Times","B",12);
      $informeCierreCaja->Cell(68,"6",utf8__("Ingreso de la clínica ") ,1,0,"L");
      $informeCierreCaja->SetFont("Times","",12);
      $informeCierreCaja->Cell(47,"6",(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda : 'S/.')." ".$dataCaja->ingreso_clinica,1,1,"R");
      $informeCierreCaja->SetDrawColor(0, 0, 128);
      $informeCierreCaja->SetX(12);
      $informeCierreCaja->SetFont("Times","B",12);
      $informeCierreCaja->Cell(68,"6",utf8__("Pagos o egresos del día "),1,0,"L");
      $informeCierreCaja->SetFont("Times","",12);
      $informeCierreCaja->Cell(47,"6",(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda : 'S/.')." ".($dataCaja->total_egreso == null ? "0.00":$dataCaja->total_egreso),1,1,"R");
     }
    
    if(self::profile()->rol === 'admin_general' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia')
    {
      $informeCierreCaja->SetDrawColor(0, 0, 128);
      /// ventas
    $informeCierreCaja->SetX(12);
    $informeCierreCaja->SetFont("Times","B",12);
    $informeCierreCaja->Cell(68,"6",utf8__("Ingresos o ventas del día"),1,0,"L");
    $informeCierreCaja->SetFont("Times","",12);
    $informeCierreCaja->Cell(47,"6",(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda : 'S/.')." ".$dataCaja->total_ventas,1,1,"R");
    

    /// compras
    $informeCierreCaja->SetDrawColor(0, 0, 128);
    $informeCierreCaja->SetX(12);
    $informeCierreCaja->SetFont("Times","B",12);
    $informeCierreCaja->Cell(68,"6",utf8__("Compras o pago a proveedores "),1,0,"L");
    $informeCierreCaja->SetFont("Times","",12);
    $informeCierreCaja->Cell(47,"6",(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda : 'S/.')." ".$dataCaja->total_compras,1,1,"R");
    
    }
    
    /// saldo entregado al final del cierre
    $informeCierreCaja->SetX(12);
    $informeCierreCaja->SetFillColor(65, 105, 225);
    $informeCierreCaja->SetTextColor(248, 248, 255);
    $informeCierreCaja->SetFont("Times","B",12);
    $informeCierreCaja->Cell(68,"6",utf8__("Saldo entregado al final del cierre "),1,0,"L",true);
    $informeCierreCaja->SetFont("Times","B",12);
    $informeCierreCaja->Cell(47,"6",(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda : 'S/.')." ".$SaldoFin,1,1,"R",true);
    
    /// manda firma
    $informeCierreCaja->SetFillColor(0,0,0);
    $informeCierreCaja->SetTextColor(0,0,0);
    $informeCierreCaja->Ln(13);
    $informeCierreCaja->SetX(12);
    $informeCierreCaja->SetFont("Times","B",12);
    $informeCierreCaja->Cell(34,"6",utf8__("Recibí conforme :  "),0,0,"L");
    $informeCierreCaja->Cell(60,"6",utf8__("______________________________________"),0,1,"L");

    $informeCierreCaja->Ln(6);
 
    $informeCierreCaja->SetX(12);
    $informeCierreCaja->SetFont("Times","B",12);
    $informeCierreCaja->Cell(45,"6",utf8__("Usuario quién entrega :  "),0,0,"L");
    $informeCierreCaja->SetFont("Times","",12);
    $informeCierreCaja->Cell(60,"6",utf8__(self::profile()->apellidos." ".self::profile()->nombres),0,1,"L");
    $informeCierreCaja->Ln(5);

    $Fecha_ = explode("-",self::FechaActual("Y-m-d"));
    $informeCierreCaja->setFont("Times","B",12);
    $informeCierreCaja->SetX(90);
    $informeCierreCaja->Cell(180,10,self::getFechaText($Fecha_[2]."/".$Fecha_[1]."/".$Fecha_[0]),0,1);
    $informeCierreCaja->Output();
    }else{
      PageExtra::PageNoAutorizado();
    }   
  }

  /// modificar la apertura de caja
  public static function update(int $id)
  {
    self::NoAuth();
    if(self::profile()->rol === 'admin_general' ||self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia'){
      if(self::ValidateToken(self::post("_token")))
      {
       $modelCajaApertura = new Caja;

       $CajaActual = $modelCajaApertura->query()
       ->Where("estado_clinica","=","c")
       ->Or("estado_clinica","=","a")
       ->Or("estado_farmacia","=","a")
       ->Or("estado_farmacia","=","c")
       ->And("id_apertura_caja","=",$id)
       ->limit(1)->first();

       if(self::profile()->rol === 'Director')
       {
        /// calculamos el saldo final 
        $Saldo_Final = (self::post("monto_apertura_editar")+$CajaActual->ingreso_clinica) - $CajaActual->total_egreso;
        $respuesta = $modelCajaApertura->Update([
          "id_apertura_caja" => $id,
          "saldo_inicial_clinica" => self::post("monto_apertura_editar"),
          "saldo_final_clinica" => $Saldo_Final
         ]);
        
       }else{
        if(self::profile()->rol === 'Farmacia' || self::profile()->rol === 'admin_farmacia')
        {
        /// calculamos el saldo
        $Saldo_Final = (self::post("monto_apertura_editar") + $CajaActual->total_ventas) - ($CajaActual->total_egreso + $CajaActual->total_compras);
        $respuesta = $modelCajaApertura->Update([
          "id_apertura_caja" => $id,
          "saldo_inicial_farmacia" => self::post("monto_apertura_editar"),
          "saldo_final_farmacia" => $Saldo_Final
         ]);
        } 
      }
       self::json(["response" =>$respuesta]);
      }else{
        self::json(["response" => "token-invalidate"]);
      }
    }else{
      self::json(["response" => "no-authorized"],403);
    }
  }

  // update apertura de caja , si ya existe 
  public static function updateExists(int $id)
  {
    self::NoAuth();
    if( self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia'){
      if(self::ValidateToken(self::post("_token")))
      {
       $modelCajaApertura = new Caja;
 
       if(self::profile()->rol === 'Director')
       {
        /// calculamos el saldo final 
        $respuesta = $modelCajaApertura->Update([
          "id_apertura_caja" => $id,
          "fecha_apertura_clinica" => self::FechaActual("Y-m-d H:i:s"),
          "saldo_inicial_clinica" => self::post("monto_apertura_far"),
          "estado_clinica" => "a"
         ]);
        
       }else{
        if(self::profile()->rol === 'Farmacia' || self::profile()->rol === 'admin_farmacia')
        {
          $respuesta = $modelCajaApertura->Update([
            "id_apertura_caja" => $id,
            "fecha_apertura_farmacia" => self::FechaActual("Y-m-d H:i:s"),
            "saldo_inicial_farmacia" => self::post("monto_apertura_far"),
            "estado_farmacia" => "a"
           ]);
        } 
      }
       self::json(["response" =>$respuesta]);
      }else{
        self::json(["response" => "token-invalidate"]);
      }
    }else{
      self::json(["response" => "no-authorized"],403);
    }
  }
  /// eliminar caja aperturada
  public static function delete(int $id)
  {
    self::NoAuth();
    if(self::profile()->rol === 'admin_general' ||self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'Farmacia'){
      if(self::ValidateToken(self::post("_token")))
      {
       $modelCajaApertura = new Caja;

       $respuesta = $modelCajaApertura->delete($id);

       self::json(["response" =>$respuesta]);
      }else{
        self::json(["response" => "token-invalidate"]);
      }
    }else{
      self::json(["response" => "no-authorized"],403);
    }
  }

  /// confirmamos la caja de cierre
  public static function confirmarCierreCaja()
  {
    self::NoAuth();
    if(self::profile()-> rol === 'admin_general' || self::profile()->rol === 'Director' ||  self::profile()->rol === 'admin_farmacia')
    {
     return self::View_("caja.confirmar_caja_cierre");
    }else{
      PageExtra::PageNoAutorizado();
    }
  }

  /// confirmar el cierre de caja por completo

  public static function cerrarConfirmCaja(int $id)
  {
    self::NoAuth();

    if(self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general')
    {
      if(self::ValidateToken(self::post("_token")))
      {
        $modelCaja = new Caja;
        $modelEgreso = new CategoriaEgreso;

        $dataEgreso = $modelEgreso->query()->Join("subcategorias_egreso as se","se.categoriaegreso_id","=","ce.id_categoria_egreso")
        ->Where("ce.fecha_categoria","=",self::FechaActual("Y-m-d"))
        ->select("sum(se.valor_gasto) as gasto")
        ->first();

        $response = $modelCaja->Update([
          "id_apertura_caja" => $id,
          "fecha_cierre" => self::FechaActual("Y-m-d H:i:s"),
          "total_egreso" => $dataEgreso->gasto,
          "estado_caja" => "c"
        ]);

        self::json(["response" => $response]);
      }else{
        self::json(["response" => "token-invalidate"]);
      }
    }else{
      self::json(["response" => "no-authorized"]);
    }
    
  }

  /// ver reporte de caja
  public static function ReporteCajaPorFechas()
  {
    self::NoAuth();
    if(self::profile()->rol === 'Director' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general')
    {
     $reporte = new PdfResultados();
     $reporte->SetTitle("Reporte-historial-de-caja");
     $reporte->AddPage();

     /// Indicamos el tipo de letra
     $reporte->setFont("Times","B",16);
     $reporte->Cell(200,3,"Historial de caja",0,1,"C");
     $reporte->Cell(200,3,"______________________",0,1,"C");
     

     /// consultamos
     $modelCajaRepo = new Caja;

     $reporte->Ln(10);
     $reporte->setX(21);
     if(isset($_GET['fi']) and isset($_GET['ff']))
     {

      $FechaI = self::get("fi"); $FechaF = self::get("ff");

      $FechaI = explode("-",$FechaI); $FechaF = explode("-",$FechaF);

      $resultados = $modelCajaRepo->procedure("proc_reporte_caja_fechas","c",[4,self::get("fi"),self::get("ff"),"rango_fechas"]);
      
       //if(!$resultados){PageExtra::PageNoAutorizado();exit;}

      $reporte->setFont("Times","B",12);
      $reporte->SetDrawColor(119, 136, 153);
      $reporte->Cell(20,10,"Desde",1,0,"L" );
      $reporte->setFont("Times","",12);
      $reporte->Cell(60,10," ".$FechaI[2]."/".$FechaI[1]."/".$FechaI[0],1,0,"L");

      $reporte->setFont("Times","B",12);
      $reporte->Cell(20,10,"Hasta",1,0,"L" );
      $reporte->setFont("Times","",12);
      $reporte->Cell(60,10," ".$FechaF[2]."/".$FechaF[1]."/".$FechaF[0],1,1,"L");


     }
     else{
       if(isset($_GET['select_tiempo']))
       {
        $resultados = $modelCajaRepo->procedure("proc_reporte_caja_fechas","c",[self::get("select_tiempo"),"2024-03-03","2024-03-03","mes"]);
      
        //if(!$resultados){PageExtra::PageNoAutorizado();exit;}
        $reporte->setFont("Times","B",12);
        $reporte->SetDrawColor(119, 136, 153);
        $reporte->Cell(20,10,"Mes",1,0,"L");
        $reporte->setFont("Times","",12);
        $reporte->Cell(140,10," ".(strtoupper(self::getMonthName(self::get("select_tiempo"))))." DEL ".self::FechaActual("Y"),1,1,"L");
       }else{
        PageExtra::PageNoAutorizado();
        exit;
       }
     }

     $reporte->setFont("Times","B",12);
     $reporte->SetFillColor(220, 20, 60);
     $reporte->SetDrawColor(128, 128, 128);
     $reporte->SetTextColor(240, 255, 255);
     $reporte->setX(21);
     $reporte->Cell(55,10,"Fecha de apertura",1,0,"C",true);
     $reporte->Cell(55,10,"Fecha de cierre",1,0,"C",true);
     $reporte->Cell(50,10,"Total en caja ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.'),1,1,"C",true);

     /// body del reporte
     $TotalSaldo = 0.00;
     
      $reporte->setFont("Times","",12);  
      $reporte->SetTextColor(0,0,0);
      foreach ($resultados as $key => $res) {
       $reporte->setX(21);
       $reporte->Cell(55,7,$res->fechaapertura,1,0,"C");
       $reporte->Cell(55,7,$res->fechacierre,1,0,"C");
       $reporte->Cell(50,7,$res->cajatotal,1,1,"C");
       
       $TotalSaldo+= $res->cajatotal;
      }
     

     /// footer

     $reporte->setFont("Times","B",12);
     $reporte->SetFillColor(65, 105, 225);
     $reporte->SetTextColor(240, 255, 255);
     $reporte->setX(21);
     $reporte->Cell(110,7,"Total en Caja ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.'),1,0,"L",true);
     $reporte->Cell(50,7,number_format($TotalSaldo,2,","," "),1,0,"C",true);


     $reporte->Output();
    }else{
      PageExtra::PageNoAutorizado();
    }
  }
}