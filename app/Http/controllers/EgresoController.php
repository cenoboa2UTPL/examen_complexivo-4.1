<?php
namespace Http\controllers;

use Http\pageextras\PageExtra;
use lib\BaseController;
use models\CategoriaEgreso;
use models\SubCategoriaEgreso;

class EgresoController extends BaseController
{
  /** Método para visualizar la vista de egresos existentes */
  public static function index()
  {
    self::NoAuth();
    if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" || self::profile()->rol === "admin_farmacia" || self::profile()->rol === self::$profile[5]){
      self::View_("egresos.index");
    }else{
        PageExtra::PageNoAutorizado();
    }
  }
  /// crear nuevo egreso
  public static function create()
  {
    self::NoAuth();
    if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" || self::profile()->rol === "admin_farmacia"  || self::profile()->rol === self::$profile[5]){
      self::View_("egresos.create");
    }else{
        PageExtra::PageNoAutorizado();
    }
  }

  /// registrar nuevo categoria egreso
  public static function saveCategoria()
  {
    self::NoAuth();
    if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" || self::profile()->rol === "admin_farmacia" || self::profile()->rol === self::$profile[5])
    {
        if(self::ValidateToken(self::post("_token")))
        {
            $modelCategoriaEgreso = new CategoriaEgreso;

            $ExisteCategoriaEgreso = $modelCategoriaEgreso->query()->Where("name_categoria_egreso","=",trim(self::post("categoria_egreso")))->first();

            if($ExisteCategoriaEgreso){
                self::json(["response" => "existe"]);
            }else{
                $respuesta = $modelCategoriaEgreso->Insert([
                    "name_categoria_egreso" => self::post("categoria_egreso"),
                    "fecha_categoria" => self::post("fecha_categoria")
                ]);
    
                self::json(["response" => $respuesta]);
            }
        }else{
            self::json(["response" => "token-invalidate"]);
        }
    }else{
        self::json(["response" => "no-authorized"]);
    }
  }

  /// registrar nueva sub categorías de egreso
   /// registrar nuevo egreso
   public static function saveSubCategoria()
   {
     self::NoAuth();
     if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" || self::profile()->rol === "admin_farmacia" || self::profile()->rol === self::$profile[5])
     {
         if(self::ValidateToken(self::post("_token")))
         {
             $modelSubCategoriaEgreso = new SubCategoriaEgreso;
             /// obtenermos el id de la categoria de egreso
             $EgresoModelCat = new  CategoriaEgreso;

             $Egreso = $EgresoModelCat->query()->Where("name_categoria_egreso","=",trim(self::post("categoria_egreso")))->first();
             $respuesta = $modelSubCategoriaEgreso->Insert([
                 "name_subcategoria" => self::post("subcategoria_egreso"),
                 "valor_gasto" => self::post("gasto"),
                 "categoriaegreso_id" => $Egreso->id_categoria_egreso
             ]);
 
             self::json(["response" => $respuesta]);
         }else{
             self::json(["response" => "token-invalidate"]);
         }
     }else{
         self::json(["response" => "no-authorized"]);
     }
   }

   /// mostrar las categorias existentes con su respectivo subcategorias
   public static function mostrarCategoriasEgreso()
   {
    self::NoAuth();
    if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" || self::profile()->rol === "admin_farmacia" || self::profile()->rol === self::$profile[5])
    {
        $modelCategoriaEgreso = new CategoriaEgreso;

        $response = $modelCategoriaEgreso->query()->Join("subcategorias_egreso as sce","ce.id_categoria_egreso","=","sce.categoriaegreso_id")
        ->select("ce.id_categoria_egreso","name_categoria_egreso","group_concat(name_subcategoria,' ') as subcategorias","date_format(fecha_categoria,'%d/%m/%Y') as fecha")
        ->GroupBy(["ce.id_categoria_egreso"])
        ->get();

        self::json(["response" => $response]);
    }else{
        self::json(["response" => "no.authorized"]);
    }
   }

   /// Eliminamos la categoria y sus subcategorias
   public static function deleteCategoriaAndSubCategoria(int $id)
   {
     self::NoAuth();
     if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" || self::profile()->rol === "admin_farmacia"  || self::profile()->rol === self::$profile[5])
     {
         if(self::ValidateToken(self::post("_token")))
         {
             
             /// obtenermos el id de la categoria de egreso
             $EgresoModelCat = new  CategoriaEgreso;

            
             $respuesta = $EgresoModelCat->delete($id);

             
 
             self::json(["response" => $respuesta]);
         }else{
             self::json(["response" => "token-invalidate"]);
         }
     }else{
         self::json(["response" => "no-authorized"]);
     }
   }
   // mostrar las sub categorías existentes de una categoría
   public static function mostrarSubCategoriasEgreso(int $id)
   {
    self::NoAuth();
    if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" || self::profile()->rol === "admin_farmacia" || self::profile()->rol === self::$profile[5])
    {
        $modelCategoriaEgreso = new CategoriaEgreso;

        $response = $modelCategoriaEgreso->query()->Join("subcategorias_egreso as sce","ce.id_categoria_egreso","=","sce.categoriaegreso_id")
        ->select("sce.id_subcategoria","sce.name_subcategoria","valor_gasto")
        ->Where("ce.id_categoria_egreso","=",$id)
        ->get();

        self::json(["response" => $response]);
    }else{
        self::json(["response" => "no.authorized"]);
    }
   }

   /// eliminar una subcategoria
   public static function deleteSubCategoria(int $id)
   {
     self::NoAuth();
     if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" || self::profile()->rol === "admin_farmacia"  || self::profile()->rol === self::$profile[5])
     {
         if(self::ValidateToken(self::post("_token")))
         {
             
             /// obtenermos el id de la categoria de egreso
             $EgresoModelSubCat = new  SubCategoriaEgreso;

            
             $respuesta = $EgresoModelSubCat->delete($id);

             
 
             self::json(["response" => $respuesta]);
         }else{
             self::json(["response" => "token-invalidate"]);
         }
     }else{
         self::json(["response" => "no-authorized"]);
     }
   }

   /// modificar datos de la sub categoría
   public static function updateSubCategoria(int $id)
   {
     self::NoAuth();
     if(self::profile()->rol === "admin_general" || self::profile()->rol === "Director" ||  self::profile()->rol === "admin_farmacia"  || self::profile()->rol === self::$profile[5])
     {
         if(self::ValidateToken(self::post("_token")))
         {
             
             /// obtenermos el id de la categoria de egreso
             $EgresoModelSubCat = new  SubCategoriaEgreso;

            
             $respuesta = $EgresoModelSubCat->Update([
                "id_subcategoria" => $id,
                "name_subcategoria" => self::post("subcategoria_egreso"),
                 "valor_gasto" => self::post("gasto"),
             ]);
             self::json(["response" => $respuesta]);
         }else{
             self::json(["response" => "token-invalidate"]);
         }
     }else{
         self::json(["response" => "no-authorized"]);
     }
   }

    /// modificar datos de la categoría
    public static function updateCategoria(int $id)
    {
      self::NoAuth();
      if(self::profile()->rol === "admin_general" || self::profile()->rol === "admin_farmacia" || self::profile()->rol === "Director"  || self::profile()->rol === self::$profile[5])
      {
          if(self::ValidateToken(self::post("_token")))
          {
              
              /// obtenermos el id de la categoria de egreso
              $EgresoModelCat = new  CategoriaEgreso;
 
             
              $respuesta = $EgresoModelCat->Update([
                 "id_categoria_egreso" => $id,
                 "name_categoria_egreso" => self::post("name_categoria"),
                 "fecha_categoria" => self::post("fecha"),
              ]);
              self::json(["response" => $respuesta]);
          }else{
              self::json(["response" => "token-invalidate"]);
          }
      }else{
          self::json(["response" => "no-authorized"]);
      }
    }
}