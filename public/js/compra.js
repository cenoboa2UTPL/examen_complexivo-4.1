/// obtener número de la serie de la compra
function getSerieCompra()
{
  $.ajax({
    url:RUTA+"app/farmacia/compra/num_serie",
    method:"GET",
    dataType:"json",
    success:function(response){
     $('#serie_compra').val(response.response);
    }
  });
}

/** Mostrar los productos existentes para el proceso de la compra */
function showProductosParaLaCompra()
{
  TablaProductosCompra = $('#lista_search_productos_compra').DataTable({
    language: SpanishDataTable(),
    bDestroy: true,
    responsive: true,
    processing: true,
    "columnDefs": [{
        "searchable": false,
        "orderable": false,
        "targets": 0
    }],
    ajax:{
      url:RUTA+"app/farmacia/compra/productos/existentes",
      method:"GET",
      dataSrc:"response"
    },
    columns:[
      {"data":"nombre_producto"},
      {"data":"id_producto"},
      {"data":null,render:function(){
          return `<button class='btn btn-outline-success rounded btn-sm' id='add_prod_compra'><i class='bx bxs-cart-add'></i></button>`;
      }},
      {"data":"nombre_producto",render:function(productoname){
        return productoname.toUpperCase();
      }},
      {"data":null,render:function(proveedorname){
        return proveedorname.proveedor_name.toUpperCase();
      }},
      
      {"data":"name_embalaje",render:function(empaquename){
        return empaquename.toUpperCase();
      }},
      {"data":"name_tipo_producto",render:function(tiponame){
        return tiponame.toUpperCase();
      }},
      {"data":"precio_venta"},
      {"data":"name_presentacion",render:function(presentacion_name){
        return presentacion_name.toUpperCase();
      }},
      {"data":"name_grupo_terapeutico",render:function(gruponame){
        return gruponame.toUpperCase();
      }}
    ],
    columnDefs:[
      { "sClass": "hide_me", target: 1 }
    ]
  });
  /*=========================== ENUMERAR REGISTROS EN DATATABLE =========================*/
  TablaProductosCompra.on('order.dt search.dt', function() {
    TablaProductosCompra.column(0, {
        search: 'applied',
        order: 'applied'
    }).nodes().each(function(cell, i) {
        cell.innerHTML = i + 1;
    });
}).draw();
}

/// agregar producto a la cesta oara la compra
function addCestaProductoCompra()
{
  $('#lista_search_productos_compra tbody').on('click','#add_prod_compra',function(){
    let fila = $(this).parents('tr');

    if(fila.hasClass('child'))
    {
      fila = fila.prev();
    }

 
    $('#precio_compra').focus();
    $('#precio_compra').val("");$('#cantidad_compra').val("");
    $('#producto_compra').val(fila.find('td').eq(3).text());
    $('#empaque_compra').val(fila.find('td').eq(5).text());
    IDPRODUCTOCOMPRA = fila.find('td').eq(1).text();
  });
}
 
$('#precio_compra').keypress(function(evento){
 if(evento.which == 13)
 {
  evento.preventDefault();
  if($(this).val().trim().length == 0)
  {
    $(this).focus();
  }else{
    $('#cantidad_compra').focus();
  }
 }
});

$('#cantidad_compra').keypress(function(evento){
  if(evento.which == 13)
  {
   evento.preventDefault();
   if($(this).val().trim().length == 0)
   {
     $(this).focus();
   }else{
    addCestaProductos(IDPRODUCTOCOMPRA);
   }
  }
 });

 /**
  * Método para agregar productos a la cesta
  */
 function addCestaProductos(id)
 {
  $.ajax({
    url:RUTA+"app/farmacia/compra/add_cesta_productos/"+id,
    method:"POST",
    data:{
      _token:TOKEN,precio_compra:$('#precio_compra').val(),cantidad_compra:$('#cantidad_compra').val()
    },
    dataType:"json",
    success:function(response){
      if(response.response === 'compra_agregado')
        {
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Producto agregado a la cesta correctamente",
            showConfirmButton: false,
            timer: 1000,
            target:document.getElementById('modal_listado_producto_compra')
          }).then(function(){
            showProductosDeLaCestaCompra();
            $('#precio_compra').val("");$('#cantidad_compra').val("");
          });
           
        }else{
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Cantidad y precio modificado del producto ",
            showConfirmButton: false,
            timer: 1000,
            target:document.getElementById('modal_listado_producto_compra')
          }).then(function(){
            showProductosDeLaCestaCompra();
          });
        }
    }
  });
 }

 /** 
 * Mostrar los productos añadidos a la cesta
 */
function showProductosDeLaCestaCompra()
{
  let productosCesta = show_Personalice(RUTA+"app/farmacia/compra/show_productos_cesta");
 
  let tr = '';let importe = 0.00;let TotalPagar = 0.00;
  let SubTotal = 0.00;let IvaVenta = 0.00;
  if(!Array.isArray(productosCesta.response)){
   
    let productosCestaDataCompra = Object.values(productosCesta.response);
   
     
    productosCestaDataCompra.forEach(element => {
      importe = element.cantidad*element.precio;
      TotalPagar+=importe;
      SubTotal = TotalPagar / (1 + (IvaData / 100)); /// igv incluido

      IvaVenta = TotalPagar - SubTotal;
      tr+=`<tr>
      <td class="text-center"><button class="btn btn-outline-danger btn-sm rounded" id='quitar_producto_detalle'><i class='bx bx-trash'></i></button></td>
      <td class='text-center col-1'><input type="text" id="cantidad_en_cesta" class="form-control text-center" value=`+element.cantidad+`></td>
      <td class="text-center">`+element.descripcion+`</td>
      <td class="text-center">`+element.empaque.toUpperCase()+`</td>
      <td class="text-center col-2"><input type="text" id="precio_en_cesta" class="form-control text-center" value=`+element.precio+`></td>
      <td class="text-center">`+importe.toFixed(2)+`</td>
      </tr>`;;
    });
      $('#sub_total_compra').val(SubTotal.toFixed(2));
      $('#igv_compra').val(IvaVenta.toFixed(2));
      $('#total_compra').val(TotalPagar.toFixed(2))
     
  }else{
    
   tr+=`<td colspan="5"><span class="text-danger">No hay productos agregados a la cesta...</span></td>'`;
  }

  $('#lista_detalle_compra_body').html(tr);
}

/**
 * Quitar producto de la cesta
 */
function QuitarProductoCestaCompra()
{
  $('#lista_detalle_compra_body').on('click','#quitar_producto_detalle',function(){
    let fila = $(this).parents('tr');

    let ProductoText = fila.find('td').eq(2).text();

    Swal.fire({
      title: "Estas seguro de quitar el producto "+ProductoText+" de la cesta ?",
      text: "Al aceptar, automaticamente se quitará de la lista de la cesta!",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si, eliminar!"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url:RUTA+"app/farmacia/compra/quitar_productos_cesta",
          method:"POST",
          dataType:"json",
          data:{
            producto:ProductoText
          },
          success:function(response){
           if(response.response === "eliminado")
           {
            Swal.fire({
              title:"Mensaje del sistema!",
              text:"Producto quitado de la cesta correctamente!",
              icon:"success"
            }).then(function(){
              if($('#lista_detalle_compra_body tr').length == 1)
              {
                $('#igv_compra').val("0.00");$('#total_compra').val("0.00");$('#sub_total_compra').val("0.00");
                
              } 
                showProductosDeLaCestaCompra();
              
            });
           }else{
            Swal.fire({
              title:"Mensaje del sistema!",
              text:"Error al eliminar producto de la cesta!",
              icon:"error"
            });
           }
          },error:function(err){
            Swal.fire({
              title:"Mensaje del sistema!",
              text:"Error al eliminar producto de la cesta!",
              icon:"error"
            });
          }
        });  
      }
    });
  });
}

/// modificar la cantidad del producto añadido a la cesta
function modificarCantidadProductoCestaCompra()
{
 
  $('#lista_detalle_venta_body').on('keyup','#cantidad_en_cesta',function(evento){
     
       if(parseInt($(this).val()) == 0)
       {
         
         showProductosDeLaCestaCompra();
         $(this).focus(); 
       } 
 
   });

   /// modificar cantidad producto añadido a la cesta de la compra
  $('#lista_detalle_compra_body').on('keypress','#cantidad_en_cesta',function(evento){
   if(evento.which === 13)
    {
      evento.preventDefault();

      if($(this).val().trim().length > 0)
      {
        let fila = $(this).parents('tr');
        
        let ProductoModificarCantidad = fila.find('td').eq(2).text();
        $.ajax({
          url:RUTA+"app/farmacia/compra/modify_cantidad_producto_cesta",
          method:"POST",
          data:{
            producto:ProductoModificarCantidad,cantidad_cesta:$(this).val()
          },
          dataType:"json",
          success:function(response){
            if(response.response === 'modificado')
            {
              Swal.fire({
                title:"Mensaje del sistema!",
                text:"Cantidad modificado correctamente!",
                icon:"success"
              }).then(function(){
                showProductosDeLaCestaCompra();
              });
            }else{
              Swal.fire({
                title:"Mensaje del sistema!",
                text:"Error al modificar la cantidad del producto!",
                icon:"error"
              });
            }
          }
        });
      }else{
        showProductosDeLaCestaCompra();
      }
    }

  });
}

/// modificar la cantidad del producto añadido a la cesta
function modificarPriceProductoCestaCompra()
{
 
  $('#lista_detalle_venta_body').on('keyup','#precio_en_cesta',function(evento){
     
       if(parseInt($(this).val()) == 0)
       {
         
         showProductosDeLaCestaCompra();
         $(this).focus(); 
       } 
 
   });

   /// modificar cantidad producto añadido a la cesta de la compra
  $('#lista_detalle_compra_body').on('keypress','#precio_en_cesta',function(evento){
   if(evento.which === 13)
    {
      evento.preventDefault();

      if($(this).val().trim().length > 0)
      {
        let fila = $(this).parents('tr');
        
        let ProductoModificarPrecio = fila.find('td').eq(2).text();
        $.ajax({
          url:RUTA+"app/farmacia/compra/modify_precio_producto_cesta",
          method:"POST",
          data:{
            producto:ProductoModificarPrecio,precio_cesta:$(this).val()
          },
          dataType:"json",
          success:function(response){
            if(response.response === 'modificado')
            {
              Swal.fire({
                title:"Mensaje del sistema!",
                text:"Precio modificado correctamente!",
                icon:"success"
              }).then(function(){
                showProductosDeLaCestaCompra();
              });
            }else{
              Swal.fire({
                title:"Mensaje del sistema!",
                text:"Error al modificar el precio del producto!",
                icon:"error"
              });
            }
          }
        });
      }else{
        showProductosDeLaCestaCompra();
      }
    }

  });
}

/// guardar la compra
function saveCompraFarmacia()
{
  $.ajax({
    url:RUTA+"app/farmacia/compra/store",
    method:"POST",
    data:{
      _token:TOKEN,serie_compra:$('#serie_compra').val(),fecha_compra:$('#fecha_emision_compra').val(),
      proveedor_id:PROVEEDOR_ID_COMPRA
    },
    dataType:"json",
    success:function(response)
    {
     if(response.response == 1)
     {
      Swal.fire({
        title:"Mensaje del sistema!",
        text:"Compra realizado con éxito",
        icon:"success"
      }).then(function(){
    
        CancelCompraDetalle();
        getSerieCompra();
       
        $('#fecha_emision_compra').val(FechaActualVenta);
        $('#igv_compra').val("0.00");$('#total_compra').val("0.00");$('#sub_total_compra').val("0.00");
        PROVEEDOR_ID_COMPRA =null;$('#proveedor_compra').val("");
       
      });
     }
    }
  })
}

/// cancelar la compra
function CancelCompraDetalle()
{
  $.ajax({
    url:RUTA+"app/farmacia/compras/cancel",
    method:"POST",
    data:{
      _token:TOKEN
    },
    success:function(response){
      showProductosDeLaCestaCompra();
    }
  });
}

function BuscarProveedores()
{
  TablaBuscarProveedores =  $('#tabla_buscar_proveedores').DataTable({
    language: SpanishDataTable(),
    bDestroy: true,
    responsive: true,
    processing: true,
    "columnDefs": [{
        "searchable": false,
        "orderable": false,
        "targets": 0
    }],
    ajax:{
      url:RUTA+"app/farmacia/proveedores/all/no",
      method:"GET",
      dataSrc:"response"
    },
    columns:[
      {"data":"id_proveedor"},
      {"data":"id_proveedor"},
      {"data":null,render:function(){
        return `<button class='btn btn-primary btn-sm rounded' id='select_proveedor'><i class='bx bx-select-multiple'></i></button>`;
      }},
      {"data":"proveedor_name",render:function(proveedor){
        return proveedor.toUpperCase();
      }},
      {"data":"contacto_name",render:function(proveedor_contacto){
        return proveedor_contacto.toUpperCase();
      }}
    ],
    columnDefs:[
      { "sClass": "hide_me", target: 1 }
    ]
  });
  /*=========================== ENUMERAR REGISTROS EN DATATABLE =========================*/
  TablaBuscarProveedores.on('order.dt search.dt', function() {
    TablaBuscarProveedores.column(0, {
        search: 'applied',
        order: 'applied'
    }).nodes().each(function(cell, i) {
        cell.innerHTML = i + 1;
    });
}).draw();
}

/** SELECCIONAR PROVEEDOR */
function SeleccionarProveedor()
{
  $('#tabla_buscar_proveedores tbody').on('click','#select_proveedor',function(){
   let fila = $(this).parents('tr'); /// fila seleccionada

   if(fila.hasClass('child'))
   {
    fila = fila.prev();
   }

   /// obtenemos el id del proveedor
   PROVEEDOR_ID_COMPRA = fila.find('td').eq(1).text();
   let Name_Proveedor = fila.find('td').eq(3).text();
   $('#proveedor_compra').val(Name_Proveedor);
   $('#modal_open_proveedores').modal("hide");
  });
}