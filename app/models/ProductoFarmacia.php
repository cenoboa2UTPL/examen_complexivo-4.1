<?php 
namespace models;
use report\implementacion\Model;
class ProductoFarmacia extends Model
{
    protected string $Table = "productos_farmacia "; 
    protected $Alias = "as prod_far ";

    protected string $PrimayKey = "id_producto";

    /** Mostrar los productos existentes */
    public function mostrar(string $fechaActual)
    {
       return $this->query()->Join("tipo_producto_farmacia as tpf","prod_far.tipo_id","=","tpf.id_tipo_producto")
       ->Join("presentacion_farmacia as pf","prod_far.presentacion_id","=","pf.id_pesentacion")
       ->Join("laboratorio_farmacia lf","prod_far.laboratorio_id","=","lf.id_laboratorio")
       ->Join("grupo_terapeutico_farmacia as gtf","prod_far.grupo_terapeutico_id","=","gtf.id_grupo_terapeutico")
       ->Join("embalaje_farmacia ef","prod_far.empaque_id","=","ef.id_embalaje")
       ->Join("proveedores_farmacia as prof","prod_far.proveedor_id","=","prof.id_proveedor")
       ->get();
    }

    /** 
     * Registrar nuevo producto
     */
    public function saveProducto(string $productoName,float $precio,int|null $stock,int|null $stockminimo,string|null $fecha_vencimiento,
    int $tipo,int $presentacion,int $laboratorio,int $grupo,int $empaque,int $proveedorid)
    {
        $ExistePoroducto  = $this->query()->Where("nombre_producto","=",$productoName)->first();
       
        if($ExistePoroducto)
        {
            return 'existe';
        }

        return  $this->Insert([
            "nombre_producto" => $productoName,"precio_venta" => $precio,
            "stock" => is_null($stock)? 0:$stock,"stock_minimo" => is_null($stockminimo)? 5:$stockminimo,
            "fecha_vencimiento" => $fecha_vencimiento,
            "tipo_id" => $tipo,"presentacion_id" => $presentacion,
            "laboratorio_id" => $laboratorio,"grupo_terapeutico_id" => $grupo,
            "empaque_id" => $empaque,"proveedor_id" => $proveedorid
        ]);
    }

     /** 
     * Modificar nuevo producto
     */
    public function updateProducto(string $productoName,float $precio,int|null $stock,int|null $stockminimo,string|null $fecha_vencimiento,
    int $tipo,int $presentacion,int $laboratorio,int $grupo,int $empaque,int $proveedorid,int $id)
    {
         

        return  $this->Update([
            "id_producto" => $id,
            "nombre_producto" => $productoName,"precio_venta" => $precio,
            "stock" => is_null($stock)? 0:$stock,"stock_minimo" => is_null($stockminimo)? 5:$stockminimo,
            "fecha_vencimiento" => $fecha_vencimiento,
            "tipo_id" => $tipo,"presentacion_id" => $presentacion,
            "laboratorio_id" => $laboratorio,"grupo_terapeutico_id" => $grupo,
            "empaque_id" => $empaque,"proveedor_id" => $proveedorid
        ]);
    }

    /*Borrar por completo el productos registrado*/
    public function Borrar(int $id)
    {
        return $this->delete($id);
    }

    /** Habilitar e inhabilitar los productos */
    public function HabilitarInhabilitarProductosRegistrados(int $id,string $Condition,string $fechaActual)
    {
        return $this->Update([
            "id_producto" => $id,
            "deleted_at_prod" =>  $Condition === 'i' ? $fechaActual:null
        ]);
    }

}