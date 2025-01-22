@extends($this->Layouts("dashboard"))

@section("title_dashboard","Mis servicios")

@section('css')
    <style>
        #listaservicios>thead>tr>th{
            background-color: rgb(76, 105, 158);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 15px;
            color:azure;
             
        }
        #tabla_servicios_eliminados>thead>tr>th{
            background-color: rgb(76, 105, 158);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 15px;
            color:azure; 
        }
        td.hide_me
        {
        display: none;
        }
         
    </style>
@endsection
@section('contenido')
 <div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" style="background-color: aquamarine">
                <h4 class="text-primary">Servicios del médico</h4>
            </div>

            <div class="card-body">
                <div class="row mt-4">
                    <div class="col-xl-5 col-lg-5 col-md-6 col-12">
                        <label for="especialidad"><b>Seleccionar especialidad</b></label>
                        <select name="especialidad" id="especialidad" class="form-select">
                            <option disabled selected>--- Seleccionar ---</option>
                            @foreach ($especialidades as $esp)
                                <option value="{{$esp->id_especialidad}}">{{strtoupper($esp->nombre_esp)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-7 col-lg-7 col-md-6 col-12">
                        <label for="medico"><b>Seleccionar médico</b></label>
                        <select name="medico" id="medico" class="form-select">
                           
                        </select>
                    </div> 
                </div>

                <div class="row">
                    <div class="col-12 table-responsive">
                        <div class="card">
                            <div class="card-body">
                                <button class="btn btn-primary mb-2" id="addservicemedico">Agregar servicio <i class='bx bx-plus'></i></button>
                                <table class="table table-bordered table-striped nowrap responsive" id="listaservicios" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Servicio</th>
                                            <th>Imp.paciente </th>
                                            <th>Imp.Médico</th>
                                            <th>Imp.Clínica</th>
                                            <th>Acciones</th>
                                            <th class="d-none">ID</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
 </div>
 {{--modal para editar los servicios de un médico con respecto a una especialidad---}}
 <div class="modal fade" id="editar_servicio" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4>Editar servicio</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="name_servicio" class="form-label"><b>Nombre servicio <span class="text-danger">*</span></b></label>
                    <input type="text" id="name_servicio" class="form-control">
                </div>

                <div class="form-group">
                    <label for="precio_servicio" class="form-label"><b>Precio servicio <span class="text-danger">*</span></b></label>
                    <input type="text" id="precio_servicio" class="form-control">
                </div>

                <div class="form-group">
                    <label for="precio_medico_servicio" class="form-label"><b>Precio servicio (Médico) <span class="text-danger">*</span></b></label>
                    <input type="text" id="precio_medico_servicio" class="form-control">
                </div>

                <div class="form-group">
                    <label for="precio_clinica_servicio" class="form-label"><b>Precio servicio (Clínica) <span class="text-danger">*</span></b></label>
                    <input type="text" id="precio_clinica_servicio" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success rounded" id="update_service">Guardar <i class='bx bxs-save' ></i></button>
                <button class="btn btn-danger rounded" id="cerrar_modal_service">Cancelar <i class='bx bx-x' ></i></button>
            </div>
        </div>
    </div>
 </div>

 {{---MODAL PARA AGREGAR NUEVOS SERVICIOS PARA EL MEDICO---}}
 <div class="modal fade" id="add_servicio_modal" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="text-white">Registrar nuevo servicio</h4>
            </div>

            <div class="modal-body">
                <div id="formulario_add_servicio">
                    <div class="form-group">
                        <label for="name_servicio_add" class="form-label"><b>Nombre servicio <span class="text-danger">*</span></b></label>
                        <input type="text" id="name_servicio_add" class="form-control">
                    </div>
    
                    <div class="form-group">
                        <label for="precio_servicio_add" class="form-label"><b>Precio servicio <span class="text-danger">*</span></b></label>
                        <input type="text" id="precio_servicio_add" class="form-control">
                    </div>
    
                    <div class="form-group">
                        <label for="precio_medico_servicio_add" class="form-label"><b>Precio servicio (Médico) <span class="text-danger">*</span></b></label>
                        <input type="text" id="precio_medico_servicio_add" class="form-control">
                    </div>
    
                    <div class="form-group">
                        <label for="precio_clinica_servicio_add" class="form-label"><b>Precio servicio (Clínica) <span class="text-danger">*</span></b></label>
                        <input type="text" id="precio_clinica_servicio_add" class="form-control">
                    </div>
                </div>

                <a href="" id="import_excel">Importar datos por excel <i class='bx bxs-file-export'></i></a>
                <div id="formulario_add_servicio_excel" style="display: none">
                    <a href="" id="formulario_service">Ir a formulario<i class='bx bxs-file-export'></i></a>
                    <form action="" method="post" id="form_import_excel_service" enctype="multipart/form-data">
                        <input type="hidden" name="token_" value="{{$this->Csrf_Token()}}">
                        <div class="form-group">
                            <label for="file_excel" class="form-label"><b>Seleccionar un archivo excel <span class="text-danger">*</span></b></label>
                            <input type="file" name="file_excel" id="file_excel" class="form-control">
                        </div>
                    </form>
                    <div class="alert alert-danger mt-2" id="alert_import_excel_service" style="display: none">
                        Seleccione un archivo excel!
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success rounded" id="save_service">Guardar <i class='bx bxs-save' ></i></button>
                <button class="btn btn-danger rounded" id="cerrar_modal_add_service">Cancelar <i class='bx bx-x' ></i></button>
            </div>
        </div>
    </div>
 </div>
@endsection

@section('js')
    <script>
        var RUTA = "{{URL_BASE}}" // la url base del sistema
        var TOKEN = "{{$this->Csrf_Token()}}";
        var TablaServicios;
        var SERVICEID;
        var MEDICOESP;
        var accion = 'formulario';
        $(document).ready(function(){
          let Especialidad = $('#especialidad');
          let Medico = $('#medico');
          mostrarServiciosMedico();  

          Especialidad.change(function(){
             mostrarMedicosPorEspecialidad($(this).val());
             mostrarServiciosMedico(null)
          });

          Medico.change(function(){
            MEDICOESP = $(this).val();
             mostrarServiciosMedico(MEDICOESP);
          });

          $('#name_servicio_add').keypress(function(evento){
             if(evento.which == 13){
                if($(this).val().trim().length > 0){
                    $('#precio_servicio_add').focus();
                }else{
                    $(this).focus();
                }
             }
          });
          $('#precio_servicio_add').keypress(function(evento){
             if(evento.which == 13){
                if($(this).val().trim().length > 0){
                    $('#precio_medico_servicio_add').focus();
                }else{
                    $(this).focus();
                }
             }
          });
          $('#precio_medico_servicio_add').keypress(function(evento){
             if(evento.which == 13){
                if($(this).val().trim().length > 0){
                    $('#precio_clinica_servicio_add').focus();
                }else{
                    $(this).focus();
                }
             }
          });

          $('#cerrar_modal_service').click(function(){
            $('#editar_servicio').modal("hide");
          });

          $('#update_service').click(function(){
            modificarService(SERVICEID,
                             $('#name_servicio'),
                             $('#precio_servicio'),
                             $('#precio_medico_servicio'),
                             $('#precio_clinica_servicio'))
          });

          $('#addservicemedico').click(function(){
             
            if(Medico.val()!= null){
                $('#add_servicio_modal').modal("show");
                $('#name_servicio_add').focus();
            }else{
               Swal.fire(
                {
                    title:"Mensaje del sistema!",
                    text:"Seleccione a un médico para añadirle nuevos servicios!",
                    icon:"warning"
                }
               )
            }
          });
          $('#cerrar_modal_add_service').click(function(){
             $('#formulario_add_servicio_excel').hide();
             $('#formulario_add_servicio').show(400);
             $('#import_excel').show(400);
             $('#formulario_service').hide();
             $('#file_excel').val("");
             $('#alert_import_excel_service').hide();
             $()
            $('#add_servicio_modal').modal("hide");
          });

          $('#save_service').click(function(){
             if(accion === 'formulario')
              {
                if($('#name_servicio_add').val().trim().length == 0){
                $('#name_servicio_add').focus();
             }else{
                if($('#precio_servicio_add').val().trim().length == 0){
                    $('#precio_servicio_add').focus();
                }else{
                    if($('#precio_medico_servicio_add').val().trim().length == 0){
                        $('#precio_medico_servicio_add').focus();
                    }else{
                        if($('#precio_clinica_servicio_add').val().trim().length == 0){
                            $('#precio_clinica_servicio_add').focus();
                        }else{
                            saveService(MEDICOESP,
                             $('#name_servicio_add'),
                             $('#precio_servicio_add'),
                             $('#precio_medico_servicio_add'),
                             $('#precio_clinica_servicio_add'))
                        }
                    }
                }
             }
            }else{
                importService()
            }
          });

          $('#import_excel').click(function(evento){
             evento.preventDefault();
             accion = 'excel';
             $('#formulario_add_servicio_excel').show(400);
             $('#formulario_add_servicio').hide();
             $(this).hide();
             $('#formulario_service').show(400);
          });
          $('#formulario_service').click(function(evento){
             evento.preventDefault();
             accion = 'formulario';
             $('#formulario_add_servicio_excel').hide();
             $('#formulario_add_servicio').show(400);
             $('#import_excel').show(400);
             $(this).hide();
          })
        });

     function mostrarServiciosMedico(id){
            TablaServicios = $('#listaservicios').DataTable({
                bDestroy:true,
         "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": 0
                }],
                ajax:{
                    url:RUTA+"servicios-medico-por-especialidad/"+id,
                    method:"GET",
                    dataSrc:"servicios"
                },
                columns:[
                    {"data":"name_servicio"},
                    {"data":"name_servicio",render:function(precioservicio){
                        return precioservicio.toUpperCase();
                    }},
                    {"data":"precio_servicio"},
                    {"data":"precio_medico"},
                    {"data":"precio_clinica"},
                    {"data":null,render:function(){
                        return `<button class="btn btn-warning rounded btn-sm" id='editarservicio'><i class='bx bxs-edit-alt'></i></button>`;
                    }},
                    {"data":"id_servicio"}
                ],
       columnDefs:[
                { "sClass": "hide_me", target: 6 }
                ]
            });

         TablaServicios.on( 'order.dt search.dt', function () {
            TablaServicios.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
            } );
         }).draw();
         EditarServicio('#listaservicios tbody');
        }

        /// Editar el servicio
         function EditarServicio(Tbody){
            $(Tbody).on('click','#editarservicio',function(){
                let fila = $(this).parents("tr");
                $('#editar_servicio').modal("show")

                if(fila.hasClass("child")){
                    fila = fila.prev();
                }
                SERVICEID = fila.find('td').eq(6).text();
                let NameService = fila.find('td').eq(1).text();
                let PrecioService = fila.find('td').eq(2).text();
                let PrecioMedicoService = fila.find('td').eq(3).text();
                let PrecioClinicaService = fila.find('td').eq(4).text();
                $('#name_servicio').val(NameService);
                $('#precio_servicio').val(PrecioService);
                $('#precio_medico_servicio').val(PrecioMedicoService);
                $('#precio_clinica_servicio').val(PrecioClinicaService);
            })
         }

         /// modificar el servicio
         function modificarService(id,nameservice,precioservice,preciomedservice,precclinicaservice){
            $.ajax({
                url:RUTA+"service/modificar/"+id,
                method:"POST",
                data:{
                    token_:TOKEN,
                    name_servicio:nameservice.val(),
                    precio_servicio:precioservice.val(),
                    precio_medico:preciomedservice.val(),
                    precio_clinica:precclinicaservice.val()
                },
                dataType:"json",
                success:function(response){
                    
                    if(response.response === 'ok')
                     {
                        Swal.fire({
                            title:"Mensaje del sistema!",
                            text:"Servicio modificado correctamente!",
                            icon:"success",
                            target:document.getElementById('editar_servicio')
                        }).then(function(){
                            mostrarServiciosMedico(MEDICOESP);
                        });
                     }
                }
            })
         }

         function saveService(idmedicoesp,nameservice,precioservice,preciomedservice,precclinicaservice){
            $.ajax({
                url:RUTA+"service/savedata",
                method:"POST",
                data:{
                    token_:TOKEN,
                    name_servicio:nameservice.val(),
                    precio_servicio:precioservice.val(),
                    medico_esp:idmedicoesp,
                    precio_medico:preciomedservice.val(),
                    precio_clinica:precclinicaservice.val()
                },
                dataType:"json",
                success:function(response){
                    
                    if(response.response === 'ok')
                     {
                        Swal.fire({
                            title:"Mensaje del sistema!",
                            text:"Servicio registrado correctamente!",
                            icon:"success",
                            target:document.getElementById('add_servicio_modal')
                        }).then(function(){
                            mostrarServiciosMedico(MEDICOESP);
                            nameservice.val("");
                            precioservice.val("");
                            preciomedservice.val("");
                            precclinicaservice.val("");
                        });
                     }else{
                        Swal.fire({
                            title:"Mensaje del sistema!",
                            text:"Error al registrar nuevo servicio!",
                            icon:"success",
                            target:document.getElementById('add_servicio_modal')
                        })
                     }
                }
            })
         }
        /// mostrar los médicos por especialidad
        function mostrarMedicosPorEspecialidad(id){
            let option = '<option selected disabled>-- Seleccione ---</option>';
            $.ajax({
                url:RUTA+"medicos-por-especialidad/"+id,
                method:"GET",
                dataType:"json",
                success:function(medicoresponse){
                    if(medicoresponse.medicos.length > 0){
                        medicoresponse.medicos.forEach(doctor => {
                            option+=`<option value=`+doctor.id_medico_esp+`>`+(doctor.apellidos+" "+doctor.nombres).toUpperCase()+`</option>`;
                        });
                    }

                    $('#medico').html(option);
                }
            })
        }

        /// importar los servicios por excel
        function importService()
        {
            let FormImportExcelService = new FormData(document.getElementById('form_import_excel_service'));
             FormImportExcelService.append("medico_esp",MEDICOESP);
            $.ajax({
                url:RUTA+"service/importdata/excel",
                method:"POST",
                data:FormImportExcelService,
                cache:false,
                contentType:false,
                processData:false,
                dataType:"json",
                success:function(response){
                    if(response.response === 'vacio' || response.response === 'no-accept'){
                        $('#alert_import_excel_service').show(400);
                    }else{
                        if(response.response === 'ok' || response.response === 'existe'){
                            $('#alert_import_excel_service').hide();
                            Swal.fire({
                                title:"Mensaje del sistema!",
                                text:"Servicios importados correctamente!",
                                icon:"success",
                                target:document.getElementById('add_servicio_modal')
                            }).then(function(){
                                mostrarServiciosMedico(MEDICOESP);
                            });
                        }
                    }
                }
            });
        }
    </script>
@endsection