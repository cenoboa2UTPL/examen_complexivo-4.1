@extends($this->Layouts('dashboard'))

@section('title_dashboard', 'Generar-recibo')

@section('css')
    <style>
        #detalle_servicios>thead>tr>th {
            background-color: #00CED1;
            color: #FFFAF0;
        }

        #tabla_paciente_search>thead>tr>th {
            background-color: #4169E1;
            color: #E6E6FA;
        }

        #tabla_servicios>thead>tr>th {
            background-color: #4169E1;
            color: #E6E6FA;
        }

        #tabla_recibos>thead>tr>th {
            background-color: #4169E1;
            color: #E6E6FA;
        }

        #serie,#documento {
            background-color: #ccdfc0;
            color: #0070d4;
            font-style: italic;
            font-stretch: condensed;
            border: 2px solid #5491ee;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
        }
        #paciente {
            background-color: #cfcbfd;
            color: #0070d4;
            font-style: italic;
            font-stretch: condensed;
            border: 2px solid #5491ee;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
        }
    </style>
@endsection
@section('contenido')
    <div class="card-header  text-white" style="background: #87CEFA">
        <ul class="nav nav-tabs card-header-tabs border-primary" id="tab-recibo">
            <li class="nav-item bg">
                <a class="nav-link active text-primary" aria-current="true" href="#generar_recibo" id="generar_recibo__"> <i
                        class='bx bxs-file-plus'></i></i>
                    Generar recibo
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" style="color:orangered" href="#show_recibos" tabindex="-1" aria-disabled="true"
                    id="show_recibos__"><i class='bx bxs-file-doc'></i>
                    Recibos generados
                </a>
            </li>

        </ul>
    </div>
    <div class="row">
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="generar_recibo" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="col-12 mb-3">
                    <div class="card" style="background-color: #F8F8FF">
                        <div class="card-header">
                            <h5><b class="text-primary float-start">Generar recibo</b></h5>
                            <div class="float-end my-xl-0 my-lg-0 my-md-0 my-sm-0 my-3">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">
                                        <b>REC-</b>
                                    </span>
                                    <input type="text" class="form-control" id="serie" readonly
                                        value="{{ $this->FechaActual('YmdHis') . $this->profile()->id_usuario }} - {{ $IdRecibo == null ? 1 : $IdRecibo + 1 }}">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-text">
                                <h5><b>Datos del paciente</b></h5>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="# documento..." id="documento"
                                    autofocus>
                                <button class="btn btn-outline-primary" id="search_paciente"><i class="fas fa-search"></i>
                                    Buscar</button>
                            </div>
                            <div class="card-text my-3">
                                <label for="paciente" class="form-label"><b>Paciente</b></label>
                                <input type="text" class="form-control" id="paciente" disabled>
                            </div>
                            <div class="card-text my-2">
                                <h5><b>Servicios adquiridos.</b></h5>
                            </div>
                            <div class="card-text table-responsive">
                                <div class="col-auto float-end">
                                    <button class="btn btn-rounded btn-primary mb-2" id="consultar_servicios">Consultar
                                        servicios <i class="fas fa-search"></i></button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="detalle_servicios">
                                        <thead>
                                            <tr>
                                                <th scope="col">Quitar</th>
                                                <th scope="col">Cantidad</th>
                                                <th scope="col">Descripción</th>
                                                <th scope="col">Precio
                                                    <b>{{ count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->simbolo_moneda : 'S/.' }}</b>
                                                </th>
                                                <th class="scope">Importe
                                                    <b>{{ count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->simbolo_moneda : 'S/.' }}</b>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div
                                    class="col-xl-3 col-lg-3 col-md-4 col-sm-5 col-12 mt-xl-0 mt-lg-0 mt-md-0 mt-sm-0 mt-1">
                                    <button class="btn btn-rounded btn-outline-primary form-control"
                                        id="save_recibo"><b>Generar
                                            recibo <i class="fas fa-file-pdf"></i></b></button>
                                </div>

                                <div
                                    class="col-xl-3 col-lg-3 col-md-4 col-sm-4 col-12 mt-xl-0 mt-lg-0 mt-md-0 mt-sm-0  mt-1">
                                    <button class="btn btn-rounded btn-outline-danger form-control"
                                        id="cancel_recibo"><b>Cancelar
                                            <i class="fas fa-cancel"></i></b></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="show_recibos" role="tabpanel" aria-labelledby="pills-profile-tab">
                <div class="card" style="background-color: #F8F8FF">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover responsive nowrap"
                                id="tabla_recibos" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">VER</th>
                                        <th scope="col">NUM.RECIBO</th>
                                        <th scope="col">FECHA</th>
                                        <th scope="col">PACIENTE</th>
                                        <th>TOTAL IMPORTE</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{-- modal buscar los pacientes del médico--- --}}
    <div class="modal fade" id="modal_buscar_paciente" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #F8F8FF">
                    <h4 class="h4 float-start">Mis pacientes</h4>
                    <button class="btn btn-rounded btn-outline-danger" id="exit_"><b>Salir X</b></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered responsive nowrap" id="tabla_paciente_search"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col"># DOCUMENTO</th>
                                    <th scope="col">PACIENTE</th>
                                    <th scope="col">DESEA UN RECIBO?</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal buscar los servicios del médico--- --}}
    <div class="modal fade" id="modal_servicios" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #F8F8FF">
                    <h4 class="h4 float-start">Mis servicios</h4>
                    <button class="btn btn-rounded btn-outline-danger" id="exit_servicios"><b>Salir X</b></button>
                </div>
                <div class="modal-body">
                    <b>Especialidad (*)</b>
                    <select name="especialidad_" id="especialidad_" class="form-select mt-1">
                        @if (isset($Data) and count($Data) > 0)
                            @foreach ($Data as $esp)
                                <option value="{{ $esp->id_medico_esp }}">{{ $esp->nombre_esp }}</option>
                            @endforeach
                        @endif

                    </select>
                    <div class="table-responsive">
                        <table class="table table-bordered responsive nowrap" id="tabla_servicios" style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col">SERVICIO</th>
                                    <th scope="col">PRECIO</th>
                                    <th scope="col">SELECCIONAR</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ URL_BASE }}public/js/control.js"></script>
    <script>
        var RUTA = "{{ URL_BASE }}" // la url base del sistema
        var TOKEN = "{{ $this->Csrf_Token() }}";
        var TablaPacientesSearch;
        var TablaServiciosMedico;
        var ID_PACIENTE, CITA_MEDICA_ID;
        var Tabla_Recibos;
        var MONTOMEDICO;
        var MONTOCLINICA;

        $(document).ready(function() {
            showCestaDetalleServiceRecibo();
            ConfirmquitarServiceDetalle();

            /// abrir modal para buscar al paciente
            $('#search_paciente').click(function() {
                $('#modal_buscar_paciente').modal("show");
                BuscarPacienteParaRecibo();

                SeleccionarPacienteParRecibo(TablaPacientesSearch, '#tabla_paciente_search tbody')

                ConfirmCancelReciboPaciente(TablaPacientesSearch, '#tabla_paciente_search tbody');
            });

            /// abrir modal para consultar los servicios del médico para añadir a detalle del recibo
            $('#consultar_servicios').click(function() {
                $('#modal_servicios').modal("show");

                ConsultaServiciosMedico($('#especialidad_').val());

                AddCestaServiceDetalle(TablaServiciosMedico, '#tabla_servicios tbody');
            });
            /// salir de la ventana de buscar pacientes
            $('#exit_').click(function() {
                $('#modal_buscar_paciente').modal("hide");
            });
            /// salir de la ventana de servicios del médico
            $('#exit_servicios').click(function() {
                $('#modal_servicios').modal("hide");
            });

            $('#save_recibo').click(function() {
                if ($("#paciente").val().trim().length > 0) {

                    if (document.getElementById('detalle_servicios').rows.length > 2) {
                        /// aquí llamamos al método

                        saveRecibo(
                            $('#serie').val(),
                            ID_PACIENTE,
                            $('#total').text());
                    } else {
                        Swal.fire({
                            title: "Mensaje dle sistema!",
                            text: "Ingrese el servicio(s) que adquirió el paciente..",
                            icon: "error"
                        });
                    }
                } else {
                    Swal.fire({
                        title: "Mensaje dle sistema!",
                        text: "Seleccione a un paciente...",
                        icon: "error"
                    })
                }
            });

            $('#cancel_recibo').click(function() {

                Swal.fire({
                    title: "Deseas cancelar el proceso del recibo?",
                    text: "Al realizar esta acción, se reanudarán los datos!",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, Cancelar proceso!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#paciente').val("");
                        $('#documento').val("");
                        CITA_MEDICA_ID = null;
                        ID_PACIENTE = null;
                        $.ajax({
                            url: RUTA + "recibo/cancel/proceso",
                            method: "POST",
                            data: {
                                token_: TOKEN,
                            },
                            success: function(response) {
                                response = JSON.parse(response);

                                if (response.response === 'ok') {
                                    showCestaDetalleServiceRecibo();
                                }
                            }
                        });
                        $('#documento').focus();
                    }
                });
            });

            $('#tab-recibo a').on('click', function(e) {
                e.preventDefault();
                idControls = $(this)[0].id;
                if (idControls === 'show_recibos__') {
                    MostrarRecibosGenerados();
                    print_recibo(Tabla_Recibos, '#tabla_recibos tbody');
                }

                $(this).tab("show")
            })
        });

        var BuscarPacienteParaRecibo = function() {
            TablaPacientesSearch = $('#tabla_paciente_search').DataTable({
                retrieve: true,
                language: SpanishDataTable(),
                processing: true,
                responsive: true,
                ajax: {
                    url: RUTA + "pacientes_sin_recibo?token_=" + TOKEN,
                    method: "GET",
                    dataSrc: "response",
                },
                columns: [{
                        "data": "documento"
                    },
                    {
                        "data": "paciente_"
                    },
                    {
                        "data": null,
                        render: function() {
                            return `
        <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5 col-12 m-2">
        <button class="btn rounded btn-outline-success btn-sm" id='generar_recibo'><i class='bx bx-check'></i><b>Si</b></button>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5 col-12 m-2">
        <button class="btn rounded btn-outline-danger btn-sm" id='no_generar_recibo'><i class='bx bx-x' ></i><b>No</b></button>
        </div>
        </div>
        `;
                        }
                    }
                ]
            }).ajax.reload();
        }

        /**MOstrar los servicios del médico*/
        var ConsultaServiciosMedico = (id) => {
            TablaServiciosMedico = $('#tabla_servicios').DataTable({
                retrieve: true,
                language: SpanishDataTable(),
                processing: true,
                responsive: true,
                ajax: {
                    url: RUTA + "medico/mis_servicios_data_/" + id + "?token_=" + TOKEN + "&&limit=150",
                    method: "GET",
                    dataSrc: "response",
                },
                columns: [{
                        "data": "name_servicio"
                    },
                    {
                        "data": "precio_servicio"
                    },
                    {
                        "data": null,
                        render: function() {
                            return `
         <button class='btn rounded btn-outline-primary btn-sm' id='add_cesta_service'><i class='bx bxs-select-multiple'></i></button>
        `;
                        }
                    }
                ]
            }).ajax.reload(null, false)
        };

        /// realizamos la acción de generar recibo del paciente
        function SeleccionarPacienteParRecibo(Tabla, Tbody) {
            $(Tbody).on('click', '#generar_recibo', function() {
                /// obtenemos la fila seleccionada
                let filaSelect = $(this).parents('tr');

                /// verificamos para dispositivos móviles
                if (filaSelect.hasClass("child")) {
                    filaSelect = filaSelect.prev();
                }

                let Data = Tabla.row(filaSelect).data();

                $('#paciente').val(Data.paciente_);
                $('#documento').val(Data.documento);
                ID_PACIENTE = Data.id_paciente;
                CITA_MEDICA_ID = Data.id_cita_medica;
                $('#modal_buscar_paciente').modal("hide")

            });
        }
        /// realizamos la acción de añadir los servicios a la cesta
        function AddCestaServiceDetalle(Tabla, Tbody) {
            $(Tbody).on('click', '#add_cesta_service', function() {
                /// obtenemos la fila seleccionada
                let filaSelect = $(this).parents('tr');

                /// verificamos para dispositivos móviles
                if (filaSelect.hasClass("child")) {
                    filaSelect = filaSelect.prev();
                }

                let Data = Tabla.row(filaSelect).data();

                addCarritoReciboDetalle(Data.name_servicio, Data.precio_servicio, Data.id_servicio,Data.precio_medico,Data.precio_clinica);

            });
        }


        /// proceso para añadir a la cesta del detalle del recibo
        function addCarritoReciboDetalle(servicio_data, precio_data, service_id_data,pricemedico,priceclinica) {
            $.ajax({
                url: RUTA + "add_cesta_service",
                method: "POST",
                data: {
                    token_: TOKEN,
                    service: servicio_data,
                    precio: precio_data,
                    id_serv: service_id_data,
                    preciomedico:pricemedico,
                    precioclinica:priceclinica
                },
                success: function(response) {
                    response = JSON.parse(response);
                    showCestaDetalleServiceRecibo();
                }
            });


        }

        /// mostrar los servicios a la tabla detalle
        function showCestaDetalleServiceRecibo() {
            let tr = '';
            let importe = 0.00;
            let Total = 0.00,
                Igv = 0.00,
                SubTotal = 0.00;
            let ValorIVA = "{{ count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->iva_valor : 18 }}";
            $.ajax({
                url: RUTA + "services/agregado_en_carrito?token_=" + TOKEN,
                method: "GET",
                success: function(response) {
                    response = JSON.parse(response);

                    let valor = Object.values(response.response);

                    if (response.response !== 'vacio' && valor.length > 0) {

                        valor.forEach(element => {
                            importe = element.precio * element.cantidad;
                            Total += importe;
                            SubTotal = Total / (1 + (ValorIVA / 100)); /// igv incluido
                            Igv = Total - SubTotal;
                            tr += `
                <tr>
                <td><button class='btn rounded btn-outline-danger btn-sm' id='quitar'>X</button></td>    
                <td class='text-center'><b>` + element.cantidad + `</b></td>
                <td><b class='text-secondary'>` + element.servicio + `</b></td>
                <td>` + element.precio + `</td>
                <td>` + importe.toFixed(2) + `</td>
                </tr>
                `;
                        });

                        tr += `
               <tr>
               <td colspan=4><b>Importe a pagar {{ count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->simbolo_moneda : 'S/.' }}</b></td> 
               <td colspan=1 id='total'>` + Total.toFixed(2) + `</td> 
               </tr>
               <tr>
               <td colspan=4><b>Sub Total {{ count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->simbolo_moneda : 'S/.' }}</b></td> 
               <td colspan=1>` + SubTotal.toFixed(2) + `</td> 
               </tr>
               <tr>
               <td colspan=4><b>Igv {{ count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->simbolo_moneda : 'S/.' }} </b><b class='badge bg-danger'>[{{ count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->iva_valor . '%' : '18%' }}]</b></td> 
               <td colspan=1>` + Igv.toFixed(2) + `</td> 
               </tr>
               `;
                    } else {
                        tr = `
                <tr>
                 <td colspan='5'><span class='text-danger'>No hay servicios agregados....</span></td>    
                </tr>
                `;
                    }

                    $('#detalle_servicios tbody').html(tr);
                }


            })
        }

        /// quitar de la lista el servicio añadido
        function ConfirmquitarServiceDetalle() {
            $('#detalle_servicios tbody').on('click', '#quitar', function() {
                /// obtenemos la fila
                let fila = $(this).parents("tr");
                /// obtenemos el producto seleccionado
                let producto = fila.find('td').eq(2).text();

                Swal.fire({
                    title: "Estas seguro?",
                    text: "Al aceptarm se quitará automaticamente el servicio añadido a la lista!",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, eliminar!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        QuitarServiceDetalle(producto);
                    }
                });
            });
        }

        /// proceso para quitar de la lista al servicio
        function QuitarServiceDetalle(servicio) {
            $.ajax({
                url: RUTA + "quitar_service_detalle",
                method: "POST",
                data: {
                    token_: TOKEN,
                    service: servicio
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.response === 'eliminado') {
                        showCestaDetalleServiceRecibo();
                    } else {
                        Swal.fire({
                            title: "Mensaje del sistema!",
                            text: "Error al quitar el servicio de la lista",
                            icon: "error"
                        });
                    }
                }
            });
        }

        /// método que realiza el registro de los datos del recibo
        function saveRecibo(number_recibo, paciente_id_data, monto_data,montomedico,montoclinica) {
            $.ajax({
                url: RUTA + "recibo/save",
                method: "POST",
                data: {
                    token_: TOKEN,
                    recibo_numero: number_recibo,
                    monto: monto_data,
                    monto_medico:montomedico,
                    monto_clinica:montoclinica,
                    citaid: CITA_MEDICA_ID,

                },
                success: function(response) {
                    response = JSON.parse(response);

                    /// validamos, según las respuestas obtenidas
                    if (response.response === 'ok') {
                        Swal.fire({
                            title: "Mensaje del sistema!",
                            text: "El recibo se ha generado sin problemas 😁",
                            icon: "success"
                        }).then(function() {
                            CITA_MEDICA_ID = null;
                            ID_PACIENTE = null;
                            location.href = RUTA + "paciente/recibo?v=" + $('#serie').val();
                        });
                    }
                }
            });
        }

        /// cancelar el recibo del paciente
        function ConfirmCancelReciboPaciente(Tabla, Tbody) {
            $(Tbody).on('click', '#no_generar_recibo', function() {
                /// obtenemos la fila seleccionada
                let fila = $(this).parents('tr');

                if (fila.hasClass("child")) {
                    fila = fila.prev();
                }

                let Data = Tabla.row(fila).data();

                let CITA_MEDICA_ID = Data.id_cita_medica;
                Swal.fire({
                    title: "Deseas cancelar su recibo para el paciente " + Data.paciente_,
                    text: "Al aceptar, automaticamente se borrará de la lista de pacientes!",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, Acepto!",
                    target: document.getElementById('modal_buscar_paciente')
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: RUTA + "recibo/cancel/update/" + CITA_MEDICA_ID,
                            method: "POST",
                            data: {
                                token_: TOKEN
                            },
                            success: function(response) {
                                response = JSON.parse(response);

                                if (response.response === 'ok') {
                                    Swal.fire({
                                        title: "Mensaje del sistema!",
                                        text: "Recibo cancelado para el paciente " +
                                            Data.paciente_,
                                        icon: "success",
                                        target: document.getElementById(
                                            'modal_buscar_paciente')
                                    }).then(function() {
                                        BuscarPacienteParaRecibo();
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Mensaje del sistema!",
                                        text: "Error al cancelar el recibo para el paciente " +
                                            Data.paciente_,
                                        icon: "error",
                                        target: document.getElementById(
                                            'modal_buscar_paciente')
                                    });
                                }
                            }
                        })
                    }
                });
            });
        }

        /** Mostrar los recibos generados**/
        function MostrarRecibosGenerados() {
            Tabla_Recibos = $('#tabla_recibos').DataTable({
                retrieve: true,
                language: SpanishDataTable(),
                processing: true,
                responsive: true,
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                }],
                ajax: {
                    url: RUTA + "recibos/generados?token_=" + TOKEN,
                    method: "GET",
                    dataSrc: "response",
                },
                columns: [{
                        "data": "numero_recibo"
                    },
                    {
                        "data": null,
                        render: function() {
                            return '<button class="btn rounded btn-outline-danger btn-sm" id="print_recibo"><i class="bx bxs-printer"></i></button>';
                        }
                    },
                    {
                        "data": "numero_recibo",
                        render: function(recibo_serie) {
                            return '<span class="badge bg-warning"><b>' + recibo_serie + '</b></span>';
                        }
                    },
                    {
                        "data": "fecha_recibo",
                        render: function(fecha) {
                            fecha = fecha.split(" ");

                            let Hora = fecha[1];
                            let nuevaFecha = fecha[0].split("-");

                            let fechaFormat = nuevaFecha[2] + "/" + nuevaFecha[1] + "/" + nuevaFecha[0];

                            return fechaFormat + " " + Hora;
                        }
                    },
                    {
                        "data": "paciente_"
                    },
                    {
                        "data": "monto_pagar",
                        render: function(monto) {
                            return '<span class="badge bg-success"><b> {{ count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->simbolo_moneda : 'S/.' }} ' +
                                monto + '</b></span>';
                        }
                    },

                ]
            }).ajax.reload();

            /*=========================== ENUMERAR REGISTROS EN DATATABLE =========================*/
            Tabla_Recibos.on('order.dt search.dt', function() {
                Tabla_Recibos.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        }

        /// imprimir recibo generado
        function print_recibo(Tabla, Tbody) {
            $(Tbody).on('click', '#print_recibo', function() {
                let fila = $(this).parents('tr');

                if (fila.hasClass("child")) {
                    fila = fila.prev();
                }

                let Data = Tabla.row(fila).data();
                window.open(RUTA + "paciente/recibo?v=" + Data.id_recibo, "_blank")

            });
        }
    </script>
@endsection
