<?php
namespace Http\controllers;

use FPDF;
use Http\pageextras\PageExtra;
use lib\BaseController;
use lib\PdfResultados;
use models\ClienteFarmacia;
use models\DetalleVenta;
use models\Empresa;
use models\ProductoFarmacia;
use models\VentasFarmacia;
use lib\QrCode as LibQrCode;
use models\CategoriaEgreso;
use models\SubCategoriaEgreso;

use function Windwalker\where;

class VentaFarmaciaController extends BaseController 
{
    use LibQrCode;
    /** Buscar Cliente para agregar a la venta */
    public static function BuscarCliente(string $num_doc)
    {
        self::NoAuth();
        /// mdoficado
        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            $modelCliente = new ClienteFarmacia;

            $respuestaServer = $modelCliente->query()->Where("num_doc","=",$num_doc)->first();

            self::json(["response" => $respuestaServer]);
        }else{
            self::json(["response"=>["no-authorized"]]);
        }
    }

    /*Consultar productos para la venta*/
    public static function ConsultarProductos()
    {
        self::NoAuth();

        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            $modelProductos = new ProductoFarmacia;

            $response = $modelProductos->query()->Join("tipo_producto_farmacia as tpf","prod_far.tipo_id","=","tpf.id_tipo_producto")
            ->Join("presentacion_farmacia as pf","prod_far.presentacion_id","=","pf.id_pesentacion")
            ->Join("laboratorio_farmacia lf","prod_far.laboratorio_id","=","lf.id_laboratorio")
            ->Join("grupo_terapeutico_farmacia as gtf","prod_far.grupo_terapeutico_id","=","gtf.id_grupo_terapeutico")
            ->Join("embalaje_farmacia ef","prod_far.empaque_id","=","ef.id_embalaje")
            ->Join("proveedores_farmacia as prof","prod_far.proveedor_id","=","prof.id_proveedor")
            ->Where("prod_far.fecha_vencimiento",">",self::FechaActual("Y-m-d"))
            ->And("prod_far.deleted_at_prod","is",null)
            ->get();

            self::json(["response" => $response]);
        }else{
            self::json(["response" => []]);
        }
    }

    /** Añadir a la cesta del carrito de la venta farmacia */
    public static function addCestaProducto(int $id)
    {
        self::NoAuth();
        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            /// consultamos el producto, con respecto al id seleccionado
            $ProductoModel = new ProductoFarmacia;
            
            $Producto = $ProductoModel->query()
            ->Join("embalaje_farmacia as ef","prod_far.empaque_id","=","ef.id_embalaje")
            ->Where("id_producto","=",$id)->first();

            if($Producto)
            {
                /// verificamos si existe la session carrito_farmacia
            if(!isset($_SESSION['carrito_farmacia']))
            {
               $_SESSION['carrito_farmacia'] = [];
            }

            /// verificamos si existe el prodcuto. Si existe solo aumentamos la cantida

            if(!array_key_exists($Producto->nombre_producto,$_SESSION["carrito_farmacia"]))
            {
              $_SESSION["carrito_farmacia"][$Producto->nombre_producto]["descripcion"] = $Producto->nombre_producto; 
              $_SESSION["carrito_farmacia"][$Producto->nombre_producto]["precio"] = $Producto->precio_venta;  
              $_SESSION["carrito_farmacia"][$Producto->nombre_producto]["cantidad"] = 1;
              $_SESSION["carrito_farmacia"][$Producto->nombre_producto]["empaque"] = $Producto->name_embalaje;
              $_SESSION["carrito_farmacia"][$Producto->nombre_producto]["producto_id"] = $Producto->id_producto;

              self::json(["response" => "agregado"]);
            }else{
              $_SESSION["carrito_farmacia"][$Producto->nombre_producto]["cantidad"]+=1;

              self::json(["response" => "agregado doble"]);
            }
            
            }
        }else{
            self::json(["response"=>"no-authorized"]);
        }
    }
    
    /** 
     * Mostrar productos agregados a la cesta
     */
    public static function showProductosCesta()
    {
        self::NoAuth();

        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            if(isset($_SESSION["carrito_farmacia"]))
            {
                self::json(["response"=>self::getSession("carrito_farmacia")]);
            }else{
                self::json(["response"=>[]]);  
            }
        }else{
            self::json(["response"=>[]]);
        }
    }

    /**
     * Quitar productos de la cesta
     */
    public static function QuitarProductoCesta()
    {
        self::NoAuth();

        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            if(isset($_SESSION["carrito_farmacia"][self::post("producto")]))
            {
              unset($_SESSION["carrito_farmacia"][self::post("producto")]);
              self::json(["response" => "eliminado"]);
            }
            else{
                self::json(["response" => "error a eliminar"]);
            }
        }else{
            self::json(["response" => "no-authorized"]);
        }
    }

    /// modificar la cantidad de la cesta
    public static function ModifyCantidadProductoCesta()
    {
        self::NoAuth();

        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            if(isset($_SESSION["carrito_farmacia"][self::post("producto")]))
            {
              $modelProductoStock = new ProductoFarmacia;

              $StockSuficiente = $modelProductoStock->query()
              ->select("stock")
              ->Where("nombre_producto","=",self::post("producto"))->first();

              if(self::post("cantidad_cesta") <= $StockSuficiente->stock){
                $_SESSION["carrito_farmacia"][self::post("producto")]["cantidad"] = self::post("cantidad_cesta");
                self::json(["response" => "modificado"]);
              }else{
                self::json(["response" => "stock-insuficiente"]);
              }
              
            }
            else{
                self::json(["response" => "error a modificar"]);
            }
        }else{
            self::json(["response" => "no-authorized"]);
        }
    }

    /// Cancelar la venta 
    public static function CancelVentaFarmacia()
    {
        self::NoAuth();

        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            if(self::ValidateToken(self::post("_token")))
            {
                
                   self::destroySession("carrito_farmacia"); 

                   self::json(["response" => "carrito-vacio"]);
                 
            }else{
                self::json(["response" => "token-invalidate"]);
            }
        }else{
            self::json(["response" => "no-authorized"]);
        }
    }

    /// obtener la serie de la venta
    public static function getSerieVenta()
    {
        self::NoAuth();
        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        { 
            $modelVenta = new VentasFarmacia;
            $IndexVenta = $modelVenta->ObtenerMaxVenta()->num_venta;
            $serieVenta  = self::FechaActual('YmdHis').self::profile()->id_usuario." - ". ($IndexVenta == null ? 1 : $IndexVenta+ 1) ;
            self::json(["response" => $serieVenta]);
            
        }else{
            self::json(["response" => "no-authorized"]);
        }
    }

    /// Guardar la venta
    public static function saveVenta()
    {
      /// verificamos que esté authenticado
      self::NoAuth();
      /// verificamos que seal el farmacetico(a) quien realice esta acción
      if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
      {
        if(self::ValidateToken(self::post("_token")))
        {
            self::GuardadoProcesoVenta();
        }else{
            self::json(["response" => "token-invalidate"]);
        }
      }else{
        self::json(["response" => "no-authorized"]);
      }
    }

    /// proceso para guardar la venta
    private static function GuardadoProcesoVenta()
    {
        $modelVenta = new VentasFarmacia;

        /// calculamos el total de la venta
        if(self::ExistSession("carrito_farmacia"))
        {
            $TotalDeLaVenta = 0.00;$ImportePorProducto = 0.00;$ImporteDetalle = 0.00;
            foreach($_SESSION["carrito_farmacia"] as $carritoVenta)
            {
                $ImportePorProducto = $carritoVenta["precio"] * $carritoVenta["cantidad"];
                $TotalDeLaVenta+= $ImportePorProducto;
            }
            $respuestaVenta = $modelVenta->Insert([
                "num_venta" => self::post("serie_venta"),
                "fecha_emision" => self::post("fecha_emision"),
                "cliente_id" => self::post("cliente_id"),
                "usuario_id" => self::profile()->id_usuario,
                "monto_recibido" => self::post("monto_recibido"),
                "vuelto" => self::post("vuelto"),
                "total_venta" => $TotalDeLaVenta
            ]);

            if($respuestaVenta)
            {
                /// obtenemos el id de la venta
                $VentaId = $modelVenta->query()->Where("num_venta","=",self::post("serie_venta"))->first();

                $modelDetalleVenta = new DetalleVenta;

                foreach($_SESSION["carrito_farmacia"] as $cestaDetalle)
                {
                    $ImporteDetalle = $cestaDetalle["precio"] * $cestaDetalle["cantidad"];
                    $respuestaDetalleVenta = $modelDetalleVenta->Insert([
                        "venta_id" => $VentaId->id_venta,"producto" => $cestaDetalle["descripcion"],
                        "cantidad" => $cestaDetalle["cantidad"],"precio_venta" => $cestaDetalle["precio"],
                        "importe_venta" => $ImporteDetalle,"producto_id" => $cestaDetalle["producto_id"]
                    ]);
                }

                 self::json(["response" => $respuestaDetalleVenta]);
            }else{
                self::json(["response" => "error-venta"]);
            }
        }

    }

    /**
     * Ver historial de las ventas
     */
    public static function mostrarHistorialVentas()
    {
        self::NoAuth();

        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            $modelVentaFarmacia = new VentasFarmacia;
            $respuesta = $modelVentaFarmacia->procedure("proc_historial_ventas","c",[self::get("fecha_venta")]);
            self::json(["response" => $respuesta]);
        }else{
            self::json(["response" => []]);
        }
    }

    /// imprimir ticket de venta
    public static function GenerateTicket(){
        self::NoAuth();
    if(isset($_GET['v']) and (self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0]))
     {
        // capturamos el id de recibo
        
        $VentaId = self::get("v");
      

        $modelVenta = new VentasFarmacia;

        $dataRecibo = $modelVenta->query()
        ->LeftJoin("clientes_farmacia as cf","vf.cliente_id","=","cf.id_cliente")
        ->Where("vf.num_venta","=",$VentaId)
        ->first();

        if($dataRecibo)
        {
            $pdf = new FPDF("P","mm",[73,258]);
            $pdf->SetTitle("ticket");
        
            $pdf->AddPage();
        
            /// consultamos el logo de la empresa
            $empresa = new Empresa;
            $DataEmpresa = $empresa->query()
            ->limit(1)
            ->first();
           
            $pdf->Cell(210,0,$pdf->Image(isset($DataEmpresa->logo) ? "public/asset/empresa/".$DataEmpresa->logo:"public/asset/img/lgo_clinica_default.jpg",6.5,null,60,30,'PNG'),0,1,"C");
            
            $pdf->Ln(2);
            $pdf->SetFont("Courier","B",9);
            $pdf->SetX(0);
 
            $pdf->MultiCell(70,2.9,isset($DataEmpresa->nombre_empresa) ?  "Farmacia - ".utf8__($DataEmpresa->nombre_empresa):'xxxxxxxxxxxxxxxxxxxxxxxxxx' ,0,"C");

            $pdf->Ln(3);
            $pdf->SetFont("Courier","B",9);
            $pdf->SetX(0);
            $pdf->Cell(74,2,isset($DataEmpresa->ruc)  ? " RUC: ".$DataEmpresa->ruc:" RUC: xxxxxxxxxxx",0,1,"C");
        
            $pdf->Ln();
            $pdf->SetX(0);
            $pdf->Cell(74,2,isset($DataEmpresa->direccion) ? utf8__($DataEmpresa->direccion):'xxxxxxxxxxxxxxxxxxxxx',0,1,"C");
        
            $pdf->Ln(3);
            $pdf->SetX(0);
            $pdf->Cell(74,2,utf8__("Teléfono")." : ".(isset($DataEmpresa->telefono) ? $DataEmpresa->telefono:'xxx xxx xxx'),0,1,"C");
         
            $pdf->Ln(1);
            $pdf->SetFont("Courier","",8);
        
            
            $pdf->Ln(2);
            $pdf->SetX(4);
            $pdf->setFont("Courier","B",8);
            $pdf->Cell(10,3,utf8__("Fecha de emisión : "),0,0,"L");
            $pdf->SetX(34);
            $pdf->setFont("Courier","",8);
            $pdf->Cell(10,3,$dataRecibo->fecha_emision,0,1);
        
            $pdf->Ln(2);
            $pdf->SetX(4);
            $pdf->setFont("Courier","B",8);
            $pdf->Cell(4,3,utf8__("N° de venta : "),0,0,"L");
            $pdf->SetX(29);
            $pdf->setFont("Courier","",8);
            $pdf->Cell(10,3,$dataRecibo->num_venta,0,1);
        
            $pdf->setFont("Courier","B",8);
            $pdf->Ln(3);
            $pdf->SetX(0);
            $pdf->MultiCell(74,2,!is_null($dataRecibo->cliente_id)?utf8__($dataRecibo->apellidos." ".$dataRecibo->nombres):utf8__('PÚBLICO EN GENERAL'),0,"C");
        
             
        
            $pdf->Ln(3);
            $pdf->SetX(0);
            $pdf->Cell(74,3,"---------------------------------------",0,1,"C");
        
            /*** GENERANDO EL DETALLE DE LA BOLETA***** */
            $pdf->Ln(3);
            $pdf->SetX(3);
            $pdf->MultiCell(10, 0, "CANT", 0, "C");
            
            $pdf->SetX(14);
            $pdf->MultiCell(21, 0, "DESCRIPCION", 0, "C");
    
        
            $pdf->SetX(34);
            $pdf->MultiCell(21, 0, "P.UNITARIO", 0, "C");
     
            $pdf->SetX(54);
            $pdf->MultiCell(15, 0, "IMPORTE", 0, "C");
        
            $pdf->Ln(3);
            $pdf->SetX(0);
            $pdf->Cell(74,3,"---------------------------------------",0,1,"C");
         
            /// body
            $modelDetalle = new DetalleVenta;$Total = 0.00;$SubTotal = 0.00;$Igv_ = 0.00;
            $valorIva_ = count(self::BusinesData()) == 1 ? self::BusinesData()[0]->iva_valor:18;
            $DetalleRespuesta = $modelDetalle->query()
            ->Join("ventas_farmacia as vf","dv.venta_id","=","vf.id_venta")
            ->Where("vf.id_venta","=",self::get("v"))
            ->Or("vf.num_venta","=",$VentaId)
            ->get();
             
            $pdf->SetFont("Courier","",7);
         
            foreach($DetalleRespuesta as $respuesta):
             $Total+= $respuesta->importe_venta;
             $SubTotal = $Total / (1+($valorIva_/100));
             $Igv_ = $Total - $SubTotal;
        
            $pdf->MultiCell(60,3,utf8__($respuesta->producto),0,"C");
            $pdf->Ln(3);
            $pdf->SetX(5);
            $pdf->Write(2.8,$respuesta->cantidad);
        
             
            $pdf->SetX(34);
            $pdf->Cell(12,0,$respuesta->precio_venta,0,1,"L");
         
            $pdf->SetX(54);
            $pdf->Cell(12,0,$respuesta->importe_venta,0,1,"L");
            
            $pdf->Ln(3);
            $pdf->SetX(3);
            $pdf->Cell(67,2,"-------------------------------------------",0,0,"C");
            $pdf->Ln(3);
         
            endforeach;
            $pdf->SetFont("Courier","B",7);
  
            $pdf->SetX(4);
            $pdf->Cell(20,3,"TOTAL A PAGAR ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.'),0,0,"L");
            $pdf->SetFont("Courier","",7);
            $pdf->SetFont("Courier","",8);
            $pdf->SetX(54);
            $pdf->Cell(20,3,number_format($Total,2,","," "),0,1,"L");
         
        
            $pdf->Ln();
            $pdf->SetFont("Courier","B",8);
            $pdf->SetX(4);
            $pdf->Cell(20,3,"SUB TOTAL ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.'),0,0,"L");
        
            $pdf->SetFont("Courier","",8);
            $pdf->SetX(54);
            $pdf->Cell(20,3,number_format($SubTotal,2,","," "),0,1,"L");
        
            $pdf->Ln();
            $pdf->SetFont("Courier","B",8);
            $pdf->SetX(4);
            $pdf->Cell(20,3,"IGV ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda." => ".self::BusinesData()[0]->iva_valor."%" :'S/. => 18%'),0,0,"L");
        
            $pdf->SetFont("Courier","",8);
            $pdf->SetX(54);
            $pdf->Cell(20,3,number_format($Igv_,2,","," "),0,1,"L");
        
            $pdf->Ln(3);
            $pdf->SetX(0);
            $pdf->Cell(74,3,"---------------------------------------",0,1,"C");
 
            /// codigo qr de prueba
            self::GenerateQr(isset($DataEmpresa->ruc)?$DataEmpresa->ruc:'xxxxxxxxxx'."|".$dataRecibo->numero_venta."|".number_format($Igv_,2,","," ")."|".number_format($Total,2,","," ")."|".$dataRecibo->fecha_emision."|".
            utf8__($dataRecibo->apellidos." ".$dataRecibo->nombres));
        
            $pdf->SetX(27);
            $pdf->Cell(74,0,$pdf->Image(URL_BASE.self::getDirectorioQr(),null,null,18,18),0,1,"C");
            $pdf->Ln(0);
            $pdf->setX(5);
            $pdf->setFont("Courier","B",8);
            $pdf->Cell(65,3,"Gracias por la preferencia",0,1,"C");
            $pdf->Cell(57,3,"Vuelva pronto!",0,1,"C");
        
            $pdf->Output();   
        
            unlink(self::$DirectorioQr);
        }else
        {
           PageExtra::PageNoAutorizado(); 
        }
    }else
    {
        PageExtra::PageNoAutorizado(); 
    }
    }

    /// reporte de productos para saber las ganancias, acorde al precio de venta y precio de compra
    public static function showGananciasRepoProductos()
    {
        self::NoAuth();
        if(self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
          $modelProductos = new ProductoFarmacia;

          $response = $modelProductos->procedure("proc_productos_calculo_ganancia","c",[self::get("fi"),self::get("ff")]);

          self::json(["response" => $response]);
        }else{
            self::json(["response" => []]);
        }
    }
    /// ver resultados en pdf
    public static function resultados()
    {
        self::NoAuth();

        if(self::profile()->rol === self::$profile[0] || self::profile()->rol === self::$profile[5] || self::profile()->rol === self::$profile[0])
        {
            /// creamos el pdf
            $pdfResultados = new PdfResultados();

            /// indicamos un título a la hoja
            $pdfResultados->SetTitle("Estado de resultados");

            /// agregamos una nueva hoja
            $pdfResultados->AddPage();

            /// indicamos los datos de la empresa
            
            /// Agremos un título a la hoja

            $pdfResultados->SetFont("Times","B",16);
            $pdfResultados->Ln(5);

            $pdfResultados->Cell(200,2,"Estado de resultados",0,1,"C");
            $pdfResultados->Cell(200,2,"_____________________________",0,1,"C");

            $pdfResultados->Ln(15);
            $modelProductos = new ProductoFarmacia;


            if(!isset($_GET["fi"]) or !isset($_GET["ff"])){
                PageExtra::PageNoAutorizado();
                return;
            }
            $responseGanancia = $modelProductos->procedure("proc_productos_calculo_ganancia","c",[self::get("fi"),self::get("ff")]);
            // if(!$responseGanancia){
            //     PageExtra::PageNoAutorizado();
            //     return;
            // }
            $TotalCompra = 0.00;$TotalVenta = 0.00;$UtilidadBruta = 0.00;$UtilidadPerdidaNeta = 0.00;
            $Ganancia = 0.00;
            foreach($responseGanancia as $resp){
              $TotalCompra+=$resp->precio_de_compra;
              $TotalVenta+=$resp->precio_venta;
              $UtilidadBruta = $TotalVenta-$TotalCompra;
              $Ganancia+=$resp->ganancia;
            }

            $pdfResultados->setFont("Times","B",12);
            $pdfResultados->SetTextColor(0,0,128);
            $pdfResultados->setX(20);
            $pdfResultados->Cell(60,7,utf8__("Total precio de venta ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.')),1,0,1);
            $pdfResultados->SetTextColor(0,0,0);
            $pdfResultados->Cell(110,7,number_format($TotalVenta,2,","," "),1,1,"R");

            $pdfResultados->setFont("Times","B",12);
            $pdfResultados->SetTextColor(0,0,128);
            $pdfResultados->setX(20);
            $pdfResultados->Cell(60,7,utf8__("Total precio de compra ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.')),1,0,1);
            $pdfResultados->SetTextColor(0,0,0);
            $pdfResultados->Cell(110,7,number_format($TotalCompra,2,","," "),1,1,"R");

            $pdfResultados->setFont("Times","B",12);
            $pdfResultados->SetTextColor(0,0,128);
            $pdfResultados->setX(20);
            $pdfResultados->Cell(60,7,utf8__("Utilidad bruta ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.')),1,0,1);
            $pdfResultados->SetTextColor(0,0,0);
            $pdfResultados->Cell(110,7,number_format($UtilidadBruta,2,","," "),1,0,"R");

            $pdfResultados->Ln(10);
            /// mostramos las categorias por fecha
            $modelCategoria = new CategoriaEgreso;
            $modelSub = new SubCategoriaEgreso;

            $responseCategoria = $modelCategoria->query()->Where("fecha_categoria",">=",self::get("fi"))
            ->And("fecha_categoria","<=",self::get("ff"))->get();

             
            $pdfResultados->Ln(5);$TotalEgreso = 0.00;$TotalEgresoCategoria = 0.00;$item = 0;
            foreach($responseCategoria as $cat)
            {
                $item++;
                $responseSub = $modelSub->query()
                ->Where("categoriaegreso_id","=",$cat->id_categoria_egreso)->get();
                //->select("group_concat('  ',concat(name_subcategoria,' => ',valor_gasto),' ')as datasub","sum(valor_gasto) as gasto")


                foreach($responseSub as $sub)
                {
                    
                $TotalEgreso+= $sub->valor_gasto;
                }
                $TotalEgresoCategoria+=$TotalEgreso;

                $pdfResultados->setFont("Times","B",12);
                $pdfResultados->SetTextColor(248, 248, 255);
                $pdfResultados->SetFillColor(65, 105, 225);
                $pdfResultados->SetDrawColor(65, 105, 225);
                $pdfResultados->setX(20);
                $pdfResultados->Cell(170,7,utf8__($item.".- ".$cat->name_categoria_egreso." ( ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.')." ) "." = ".$TotalEgreso),1,1,"L",true);
                $pdfResultados->setFont("Times","",10);
                $pdfResultados->SetTextColor(120,50,50);
                $pdfResultados->SetFillColor(248, 248, 255);
                foreach($responseSub as $sub)
                {
                    
                    $pdfResultados->setX(20);
                    $pdfResultados->Cell(140,7,utf8__($sub->name_subcategoria),1,0,'L',true);
 
                    $pdfResultados->Cell(30,7,(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.')." ".utf8__($sub->valor_gasto),1,1,'R',true);
                }
            }
            $pdfResultados->setFont("Times","B",12);
            $pdfResultados->SetTextColor(0,0,128);
            $pdfResultados->setX(20);
            $pdfResultados->Cell(50,7,utf8__("Total Egreso ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.')),1,0,1);
            $pdfResultados->SetTextColor(0,0,0);
            $pdfResultados->Cell(120,7,number_format($TotalEgresoCategoria,2,","," "),1,1,"R");

            $pdfResultados->SetTextColor(0,0,128);
            $pdfResultados->setX(20);
            $pdfResultados->Cell(50,7,utf8__("Utilidad o perdida neta ".(count(self::BusinesData()) == 1 ? self::BusinesData()[0]->simbolo_moneda:'S/.')),1,0,1);
            $pdfResultados->SetTextColor(0,0,0);

            $UtilidadPerdidaNeta = $Ganancia-$TotalEgresoCategoria;
            $pdfResultados->Cell(120,7,$UtilidadPerdidaNeta <=0? number_format(abs($UtilidadPerdidaNeta),2,","," ")." ".utf8__("Pérdida") : number_format(abs($UtilidadPerdidaNeta),2,","," ")." ".utf8__("Ganancia"),1,0,"R");
            /// vemos la hoja
            $pdfResultados->Output();

        }else{
            PageExtra::PageNoAutorizado();
        }
    }

    /// reporte de ventas por mes 
    public static function reporteVentasGraficoEstadicstico(string $tipo)
    {
        self::NoAuth();

        if(self::profile()->rol === 'Farmacia' || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general' || self::profile()->rol === self::$profile[0]){
            $model = new VentasFarmacia;

            $response = $model->procedure("proc_ventas_farmacia_reporte_grafico","c",[$tipo]);

            self::json(["response" => $response]);
        }else{
            self::json(["response" =>[]]);
        }
    }

    /// mostrar la cantidad de ventas realizadas a cada producto
    public static function CantidadVentasPorProducto(string $opc)
    {
        self::NoAuth();

        if(self::profile()->rol === 'Farmacia'  || self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general' || self::profile()->rol === "Director")
        {
            $modelp = new DetalleVenta;

            switch($opc){
                case "todos":

                    $respuesta = $modelp->query()->Join("productos_farmacia as pf","dv.producto_id","=","pf.id_producto")
                    ->Join("ventas_farmacia as vf","dv.venta_id","=","vf.id_venta")
                    ->select("dv.producto","sum(cantidad) as cantidad_ventas")
                    ->GroupBy(["dv.producto"])->get();
                    break;
                 case "mes":

                    $respuesta = $modelp->query()->Join("productos_farmacia as pf","dv.producto_id","=","pf.id_producto")
                    ->Join("ventas_farmacia as vf","dv.venta_id","=","vf.id_venta")
                    ->Where("year(vf.fecha_emision)","=",self::FechaActual("Y"))
                    ->And("month(vf.fecha_emision)","=",self::FechaActual("m"))
                    ->select("dv.producto","sum(cantidad) as cantidad_ventas")
                    ->GroupBy(["dv.producto"])->get();
                  break;

                  case "anio":

                    $respuesta = $modelp->query()->Join("productos_farmacia as pf","dv.producto_id","=","pf.id_producto")
                    ->Join("ventas_farmacia as vf","dv.venta_id","=","vf.id_venta")
                    ->Where("year(vf.fecha_emision)","=",self::FechaActual("Y"))
                    ->select("dv.producto","sum(cantidad) as cantidad_ventas")
                    ->GroupBy(["dv.producto"])->get();
                  break;
                  default:
                  $respuesta = $modelp->query()->Join("productos_farmacia as pf","dv.producto_id","=","pf.id_producto")
                  ->Join("ventas_farmacia as vf","dv.venta_id","=","vf.id_venta")
                  ->Where("year(vf.fecha_emision)","=",self::FechaActual("Y"))
                  ->And("month(vf.fecha_emision)","=",self::FechaActual("m"))
                  ->And("day(vf.fecha_emision)","=",self::FechaActual("d"))
                  ->select("dv.producto","sum(cantidad) as cantidad_ventas")
                  ->GroupBy(["dv.producto"])->get();
                  break;

                

            }

            self::json(["response" => $respuesta]);
        }else{
            self::json(["response" => []]);
        }
    }
}

 
