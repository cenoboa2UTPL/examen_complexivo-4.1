<?php
namespace Http\controllers;
use lib\BaseController;
use models\ComprasFarmacia;
use models\DetalleCompra;
use models\ProductoFarmacia;

class ComprasController extends BaseController
{

  /// obtener el número de la compra 
  public static function getSerieCompra()
    {
        self::NoAuth();
        if( self::profile()->rol === 'admin_general' || self::profile()->rol === 'admin_farmacia')
        { 
            $modelCompra = new ComprasFarmacia;
            $IndexCompra = $modelCompra->ObtenerMaxCompra()->num_compra;
            $serieCompra  = self::FechaActual('YmdHis').self::profile()->id_usuario." - ". ($IndexCompra == null ? 1 : $IndexCompra+ 1) ;
            self::json(["response" => $serieCompra]);
            
        }else{
            self::json(["response" => "no-authorized"]);
        }
    } 

    /// mostrar los productos existentes
    public static function showProductosExistentes()
    {
        self::NoAuth();

        if(self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general')
        {
            $modelProductos = new ProductoFarmacia;

            $response = $modelProductos->query()->Join("tipo_producto_farmacia as tpf","prod_far.tipo_id","=","tpf.id_tipo_producto")
            ->Join("presentacion_farmacia as pf","prod_far.presentacion_id","=","pf.id_pesentacion")
            ->Join("laboratorio_farmacia lf","prod_far.laboratorio_id","=","lf.id_laboratorio")
            ->Join("grupo_terapeutico_farmacia as gtf","prod_far.grupo_terapeutico_id","=","gtf.id_grupo_terapeutico")
            ->Join("embalaje_farmacia ef","prod_far.empaque_id","=","ef.id_embalaje")
            ->Join("proveedores_farmacia as prof","prod_far.proveedor_id","=","prof.id_proveedor")
            ->Where("prod_far.deleted_at_prod","is",null)
            ->get();

            self::json(["response" => $response]);
        }else{
            self::json(["response" => []]);
        }
    }

 
    /** Añadir a la cesta del carrito de la compra de productos */
    public static function addCestaProductoCompra(int $id)
    {
        self::NoAuth();
        if(self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general')
        {
           if(self::ValidateToken(self::post("_token")))
           {
             /// consultamos el producto, con respecto al id seleccionado
             $ProductoModel = new ProductoFarmacia;
            
             $Producto = $ProductoModel->query()
             ->Join("embalaje_farmacia as ef","prod_far.empaque_id","=","ef.id_embalaje")
             ->Where("id_producto","=",$id)->first();
 
             if($Producto)
             {
                 /// verificamos si existe la session carrito_farmacia
             if(!isset($_SESSION['carrito_farmacia_compra']))
             {
                $_SESSION['carrito_farmacia_compra'] = [];
             }
 
             /// verificamos si existe el prodcuto. Si existe solo aumentamos la cantida
 
             if(!array_key_exists($Producto->nombre_producto,$_SESSION["carrito_farmacia_compra"]))
             {
               $_SESSION["carrito_farmacia_compra"][$Producto->nombre_producto]["descripcion"] = $Producto->nombre_producto; 
               $_SESSION["carrito_farmacia_compra"][$Producto->nombre_producto]["precio"] = self::post("precio_compra");  
               $_SESSION["carrito_farmacia_compra"][$Producto->nombre_producto]["cantidad"] = self::post("cantidad_compra");
               $_SESSION["carrito_farmacia_compra"][$Producto->nombre_producto]["empaque"] = $Producto->name_embalaje;
               $_SESSION["carrito_farmacia_compra"][$Producto->nombre_producto]["producto_id"] = $Producto->id_producto;
 
               self::json(["response" => "compra_agregado"]);
             }else{
               $_SESSION["carrito_farmacia_compra"][$Producto->nombre_producto]["precio"] = self::post("precio_compra");    
               $_SESSION["carrito_farmacia_compra"][$Producto->nombre_producto]["cantidad"] = self::post("cantidad_compra");
 
               self::json(["response" => "compra_agregado_modificado"]);
             }
             
             }
           }else{
            self::json(["response" => "token-invalidate"]);
           }
        }else{
            self::json(["response"=>"no-authorized"]);
        }
    }

     /** 
     * Mostrar productos agregados a la cesta
     */
    public static function showProductosCestaCompra()
    {
        self::NoAuth();

        if(self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general'  )
        {
            if(isset($_SESSION["carrito_farmacia_compra"]))
            {
                self::json(["response"=>self::getSession("carrito_farmacia_compra")]);
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
    public static function QuitarProductoCestaCompra()
    {
        self::NoAuth();

        if(self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general' )
        {
            if(isset($_SESSION["carrito_farmacia_compra"][self::post("producto")]))
            {
              unset($_SESSION["carrito_farmacia_compra"][self::post("producto")]);
              self::json(["response" => "eliminado"]);
            }
            else{
                self::json(["response" => "error a eliminar"]);
            }
        }else{
            self::json(["response" => "no-authorized"]);
        }
    }

    /// modificar la cantidad de la cesta de la compra
    public static function ModifyCantidadProductoCestaCompra()
    {
        self::NoAuth();

        if(self::profile()->rol === 'admin_general' || self::profile()->rol === 'admin_farmacia')
        {
            if(isset($_SESSION["carrito_farmacia_compra"][self::post("producto")]))
            {
              $_SESSION["carrito_farmacia_compra"][self::post("producto")]["cantidad"] = self::post("cantidad_cesta");
              self::json(["response" => "modificado"]);
            }
            else{
                self::json(["response" => "error a modificar"]);
            }
        }else{
            self::json(["response" => "no-authorized"]);
        }
    }

 /// modificar el precio de la compra de la cesta de la compra
 public static function ModifyPriceProductoCestaCompra()
 {
           self::NoAuth();
   
           if( self::profile()->rol === 'admin_general' || self::profile()->rol === 'admin_farmacia')
           {
               if(isset($_SESSION["carrito_farmacia_compra"][self::post("producto")]))
               {
                 $_SESSION["carrito_farmacia_compra"][self::post("producto")]["precio"] = self::post("precio_cesta");
                 self::json(["response" => "modificado"]);
               }
               else{
                   self::json(["response" => "error a modificar"]);
               }
           }else{
               self::json(["response" => "no-authorized"]);
           }
 }

  /// Guardar la compra
  public static function saveCompra()
  {
    /// verificamos que esté authenticado
    self::NoAuth();
    /// verificamos que seal el farmacetico(a) quien realice esta acción
    if(self::profile()->rol === 'admin_farmacia' || self::profile()->rol === "admin_general")
    {
      if(self::ValidateToken(self::post("_token")))
      {
          self::GuardadoProcesoCompra();
      }else{
          self::json(["response" => "token-invalidate"]);
      }
    }else{
      self::json(["response" => "no-authorized"]);
    }
  }

   /// proceso para guardar la venta
   private static function GuardadoProcesoCompra()
   {
       $modelCompra = new ComprasFarmacia;

       /// calculamos el total de la venta
       if(self::ExistSession("carrito_farmacia_compra"))
       {
           $TotalDeLaCompra = 0.00;$ImportePorProducto = 0.00;$ImporteDetalle = 0.00;
           foreach($_SESSION["carrito_farmacia_compra"] as $carritoCompra)
           {
               $ImportePorProducto = $carritoCompra["precio"] * $carritoCompra["cantidad"];
               $TotalDeLaCompra+= $ImportePorProducto;
           }
           $respuestaCompra = $modelCompra->Insert([
               "num_compra" => self::post("serie_compra"),
               "fecha_compra" => self::post("fecha_compra"),
               "total_compra" => $TotalDeLaCompra,
               "usuario_id" => self::profile()->id_usuario,
               "proveedor_id" => self::post("proveedor_id")
           ]);

           if($respuestaCompra)
           {
               /// obtenemos el id de la venta
               $CompraId = $modelCompra->query()->Where("num_compra","=",self::post("serie_compra"))->first();

               $modelDetalleCompra = new DetalleCompra;

               foreach($_SESSION["carrito_farmacia_compra"] as $cestaDetalle)
               {
                   $ImporteDetalle = $cestaDetalle["precio"] * $cestaDetalle["cantidad"];
                   $respuestaDetalleVenta = $modelDetalleCompra->Insert([
                       "compra_id" => $CompraId->id_compra,"producto_name" => $cestaDetalle["descripcion"],
                       "precio_compra" => $cestaDetalle["precio"],"cantidad_compra" => $cestaDetalle["cantidad"],
                       "importe_compra" => $ImporteDetalle,"producto_id" => $cestaDetalle["producto_id"]
                   ]);
               }

                self::json(["response" => $respuestaDetalleVenta]);
           }else{
               self::json(["response" => "error-venta"]);
           }
       }

   }

    /// Cancelar la venta 
    public static function CancelCompraFarmacia()
    {
        self::NoAuth();

        if(self::profile()->rol === 'admin_farmacia' || self::profile()->rol === 'admin_general')
        {
            if(self::ValidateToken(self::post("_token")))
            {
                
                   self::destroySession("carrito_farmacia_compra"); 

                   self::json(["response" => "carrito-vacio"]);
                 
            }else{
                self::json(["response" => "token-invalidate"]);
            }
        }else{
            self::json(["response" => "no-authorized"]);
        }
    }
}