/**
 * Mostrar los egresos existentes
 */
function MostrarLosEgresos()
{
    TablaEgresos = $('#tabla_egresos').DataTable({
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
            url:RUTA+"egreso/categorias/all",
            method:"GET",
            dataSrc:"response"
        },
        columns:[
            {"data":"name_categoria_egreso"},
            {"data":"id_categoria_egreso"},
            {"data":null,render:function(){
                return `<button class='btn btn-danger rounded btn-sm' id='eliminar_categoria'><i class='bx bx-x'></i></button>
                <button class='btn btn-primary rounded btn-sm' id='add_subcat'><i class='bx bx-plus'></i></button>
                <button class='btn btn-info rounded btn-sm' id='editar_subcat'><i class='bx bx-collection'></i></button>
                <button class='btn btn-warning rounded btn-sm' id='editarcategoria'><i class='bx bx-pencil'></i></button>`
            }},
            {"data":"name_categoria_egreso",render:function(namecat){
                return namecat.toUpperCase();
            }},
            {"data":"fecha"},
            {"data":"subcategorias",render:function(namesubcat){
                return namesubcat.toUpperCase();
            }},
        ],
        columnDefs:[
            { "sClass": "hide_me", target: 1 }
          ]
    });

    TablaEgresos.on('order.dt search.dt', function() {
        TablaEgresos.column(0, {
            search: 'applied',
            order: 'applied'
        }).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
}

/** 
 * Agregar a la tabla detalle las subcategorias de la categoria de un egreso
 */
function addDetalleSubCategoria(name_subcat,gasto_subcat,body)
{
    let tr =''; 

    tr+=`<tr>
    <td>`+name_subcat.val()+`</td>
    <td>`+gasto_subcat.val()+`</td>
    <td><button class='btn btn-danger rounded btn-sm' id='quitar_egreso'><i class='bx bx-trash' ></i></button></td>
    </tr>`;

    $('#'+body).append(tr);
}
/**
 * Quitar las subcategorias
 */

function quitarSubCategoria()
{
    $('#detalle_egresos_body').on('click','#quitar_egreso',function(){
        let fila = $(this).parents('tr');

        fila.remove();
    });
}

function quitarSubCategoriaIndex()
{
    $('#detalle_egresos_body_index').on('click','#quitar_egreso',function(){
        let fila = $(this).parents('tr');

        fila.remove();
    });
}

/** 
 * Guardar categoria egreso
 */
function saveCategoriaEgreso()
{
  $.ajax({
    url:RUTA+"egreso/save",
    method:"POST",
    data:{
        _token:TOKEN,
        categoria_egreso:$('#categoria_name').val(),
        fecha_categoria:$('#fecha_categoria').val()
    },
    dataType:"json",
    success:function(response)
    {
        if(response.response == 1)
        {
             Swal.fire({
                title:"Mensaje del sistema!",
                text:"Categoría registrado correctamente!",
                icon:"success" 
             }).then(function(){
                saveSubCategoriasEgreso('detalle_egresos_body','categoria_name');
                $('#categoria_name').val("");
                $('#detalle_egresos_body tr').remove();
             });
        }else{
            if(response.response === 'existe')
            {
                Swal.fire({
                    title:"Mensaje del sistema!",
                    text:"Ya existe esa categoría!",
                    icon:"warning" 
                 }).then(function(){
                    $('#categoria_name').val("");
                 });  
            }
        }
    },error:function(){
        Swal.fire({
            title:"Mensaje del sistema!",
            text:"Error al crear categoría de egreso!",
            icon:"error" 
         })
    }
  });
}

/** 
 * Guardar las sub categorías
 */
function saveSubCategoriasEgreso(body,categoria)
{
    let responseData = null ;
    $('#'+body+' tr').each(function(){
    
        let NameSubCategoriaEgreso = $(this).find('td').eq(0).text();

        let GastoSubCategoriaEgreso = $(this).find('td').eq(1).text();
        let FechaSubCategoriaEgreso = $(this).find('td').eq(2).text();

        $.ajax({
            url:RUTA+"egreso/sub_categoria/save",
            method:"POST",
            async:false,
            data:{
                _token:TOKEN,
                categoria_egreso:$('#'+categoria).val(),
                subcategoria_egreso:NameSubCategoriaEgreso,
                gasto:GastoSubCategoriaEgreso 
            },
            dataType:"json",
            success:function(response)
            {
               responseData = response.response;  
            }
          });
    });

    return responseData;
}

/// eliminar categoria
function DestroyCategoriaEgreso()
{
    $('#tabla_egresos tbody').on('click','#eliminar_categoria',function(){
        let fila = $(this).parents('tr');

        if(fila.hasClass('child'))
        {
            fila = fila.prev();
        }

        IDCATEGORIAEGRESO = fila.find('td').eq(1).text();
        
        Swal.fire({
            title: "Estas seguro de eliminar a la categoria "+fila.find('td').eq(3).text()+"?",
            text: "Al aceptar, la categoría de egreso seleccionado se eliminará por completo junto a las sub categorías que tiene asociado!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, elimianr!"
          }).then((result) => {
            if (result.isConfirmed) {
               DeleteCategoriaEgreso(IDCATEGORIAEGRESO);
            }
          });
    });
}

/// editar las categorias
function EditarCategoriaEgreso()
{
    $('#tabla_egresos tbody').on('click','#editarcategoria',function(){
        let fila = $(this).parents('tr');

        if(fila.hasClass('child'))
        {
            fila = fila.prev();
        }

        IDCATEGORIAEGRESO = fila.find('td').eq(1).text();
        let fecha =  fila.find('td').eq(4).text();

        fecha = fecha.split("/");
 
        $('#name_categoria_editar').val(fila.find('td').eq(3).text());
        $('#fecha_categoria_editar').val(fecha[2]+"-"+fecha[1]+"-"+fecha[0])
        
        $('#modal_editar_categoria').modal("show");
    });
}

/// modificar las categoria
function UpdateCategoriaEgreso(id)
{
  $.ajax({
    url:RUTA+"egreso/categoria/update/"+id,
    method:"POST",
    data:{
        _token:TOKEN,
        name_categoria:$('#name_categoria_editar').val(),
        fecha:$('#fecha_categoria_editar').val()
    },
    dataType:"json",
    success:function(response)
    {
        if(response.response == 1)
        {
             Swal.fire({
                title:"Mensaje del sistema!",
                text:"Categoría modificado correctamente!",
                icon:"success",
                target:document.getElementById('modal_editar_categoria')
             }).then(function(){
                MostrarLosEgresos();
             });
        }else{
            Swal.fire({
                title:"Mensaje del sistema!",
                text:"Error al modificar la categoría seleccionado!",
                icon:"error",
                target:document.getElementById('modal_editar_categoria') 
             })
        }
    },error:function(){
        Swal.fire({
            title:"Mensaje del sistema!",
            text:"Error al modificar la categoría seleccionado!",
            icon:"error",
            target:document.getElementById('modal_editar_categoria') 
         })
    }
  });
}

function DeleteCategoriaEgreso(id)
{
  $.ajax({
    url:RUTA+"egreso/categoria/delete/"+id,
    method:"POST",
    data:{
        _token:TOKEN,
        
    },
    dataType:"json",
    success:function(response)
    {
        if(response.response == 1)
        {
             Swal.fire({
                title:"Mensaje del sistema!",
                text:"Categoría eliminado correctamente!",
                icon:"success" 
             }).then(function(){
                MostrarLosEgresos();
             });
        }else{
            Swal.fire({
                title:"Mensaje del sistema!",
                text:"Error al eliminar la categoría seleccionado!",
                icon:"error" 
             })
        }
    },error:function(){
        Swal.fire({
            title:"Mensaje del sistema!",
            text:"Error al eliminar la categoría seleccionado!",
            icon:"error" 
         })
    }
  });
}

function addSubCategoria()
{
    $('#tabla_egresos tbody').on('click','#add_subcat',function(){
        let fila = $(this).parents('tr');

        if(fila.hasClass('child'))
        {
            fila = fila.prev();
        }
         $('#modal_add_subcategoria_egresos_index').modal("show");
        IDCATEGORIAEGRESO = fila.find('td').eq(1).text();
        let NameCategoria = fila.find('td').eq(3).text();
        $('#categoria_index').val(NameCategoria);
        
    });
}

function editarSubCategoriaDeCategoriaDeEgresos()
{
    $('#tabla_egresos tbody').on('click','#editar_subcat',function(){
        let fila = $(this).parents('tr');

        if(fila.hasClass('child'))
        {
            fila = fila.prev();
        }
         $('#modal_edit_delete_subcategoria_egresos_index').modal("show");
        IDCATEGORIAEGRESO = fila.find('td').eq(1).text();
        let NameCategoria = fila.find('td').eq(3).text();
        $('#categoria_index_edit').val(NameCategoria);
        MostrarLosSubCategoriasEgresos(IDCATEGORIAEGRESO);
        EliminarLaSubCategoria();
        EditarLaSubCategoria();
        
    });
}

/// mostrar las subcategorias
function MostrarLosSubCategoriasEgresos(id)
{
    TablaSubCategoriasData = $('#detalle_egresos_index_edit').DataTable({
        language: SpanishDataTable(),
        bDestroy: true,
        responsive: true,
        processing:true,
        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }],
        ajax:{
            url:RUTA+"egreso/subcategorias/"+id,
            method:"GET",
            dataSrc:"response"
        },
        columns:[
            {"data":"name_subcategoria"},
            {"data":"id_subcategoria"},
            {"data":null,render:function(){
                return `<button class='btn btn-danger rounded btn-sm' id='eliminar_subcategoria'><i class='bx bx-x'></i></button>
                <button class='btn btn-warning rounded btn-sm' id='editar_subcategoria'> <i class='bx bx-pencil'></i></button>`
            }},
            {"data":"name_subcategoria"},
            {"data":"valor_gasto"},
        ],
        columnDefs:[
            { "sClass": "hide_me", target: 1 }
          ]
    });

    TablaSubCategoriasData.on('order.dt search.dt', function() {
        TablaSubCategoriasData.column(0, {
            search: 'applied',
            order: 'applied'
        }).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
}


function EliminarLaSubCategoria()
{
    $('#detalle_egresos_index_edit tbody').on('click','#eliminar_subcategoria',function(){
        let fila = $(this).parents('tr');

        if(fila.hasClass('child'))
        {
            fila = fila.prev();
        }
        
        IDSUBCATEGORIAEGRESO = fila.find('td').eq(1).text();
        
        Swal.fire({
            title: "Estas seguro de eliminar la subcategoría "+fila.find('td').eq(3).text()+"?",
            text: "Al eliminarlo ya no podrás recuperarlo!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, eliminar!",
            target:document.getElementById('modal_edit_delete_subcategoria_egresos_index')
          }).then((result) => {
            if (result.isConfirmed) {
               processDeleteSubCategoria(IDSUBCATEGORIAEGRESO);
            }
          });
        
    });
}

/// editar las subcategorias
function EditarLaSubCategoria()
{
    $('#detalle_egresos_index_edit tbody').on('click','#editar_subcategoria',function(){
        let fila = $(this).parents('tr');

        if(fila.hasClass('child'))
        {
            fila = fila.prev();
        }
        $('#save_egresos_index_edit').attr("disabled",false)
        IDSUBCATEGORIAEGRESO = fila.find('td').eq(1).text();
        $('#name_subcategoria_index_edit').focus();
        $('#name_subcategoria_index_edit').select();
        $('#name_subcategoria_index_edit').val(fila.find('td').eq(3).text());
        $('#gasto_subcategoria_index_edit').val(fila.find('td').eq(4).text());
        
        
    });
}

/// proceso de eliminado de subcategoria

function processDeleteSubCategoria(id)
{
    $.ajax({
        url:RUTA+"egreso/subcategoria/delete/"+id,
        method:"POST",
        data:{
            _token:TOKEN,
            
        },
        dataType:"json",
        success:function(response)
        {
            if(response.response == 1)
            {
                 Swal.fire({
                    title:"Mensaje del sistema!",
                    text:"Sub categoría eliminado correctamente!",
                    icon:"success",
                    target:document.getElementById('modal_edit_delete_subcategoria_egresos_index') 
                 }).then(function(){
                    MostrarLosEgresos();
                   $('#modal_edit_delete_subcategoria_egresos_index').modal("hide")
                 });
            }else{
                Swal.fire({
                    title:"Mensaje del sistema!",
                    text:"Error al eliminar la sub categoría seleccionado!",
                    icon:"error" 
                 })
            }
        },error:function(){
            Swal.fire({
                title:"Mensaje del sistema!",
                text:"Error al eliminar la sub categoría seleccionado!",
                icon:"error" 
             });
        }
      });
}
