@extends($this->Layouts("dashboard"))

@section("title_dashboard","Historial de ingresos por médico")

@section('css')
<style>
    #historial_ingresos>thead>tr>th {

        padding: 20px;
        background-color: #483D8B;
        color:white;
    }
 
</style>
@endsection
@section('contenido')
 <div class="row">
    <div class="col-12">
        <div class="card">
    
            <div class="card-body">
                <div class="card-text py-2">
                    <h3>Historial de ingresos</h3>
                </div>
                <div class="row">
                    <div class="col-12 mt-2">
                        <div class="form-group">
                            <label for="medico"><b>Seleccionar médico</b></label>
                            <select name="medico" id="medico" class="form-select"></select>
                        </div>
                    </div>

                    <div class="col-12 table-responsive">
                        <table class="table table-bordered table-striped nowrap" id="historial_ingresos" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>MES</th>
                                    <th>MONTO {{count($this->BusinesData()) == 1 ? $this->BusinesData()[0]->simbolo_moneda:'S/.'}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
        
                                <div class="card-text">
                                    <div id="reporte_historial"></div>
                                </div>
        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
@endsection

@section('js')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{ URL_BASE }}public/js/control.js"></script>
    <script>
        var TablaHistorial;
        var RUTA = "{{URL_BASE}}" // la url base del sistema
        var TOKEN = "{{$this->Csrf_Token()}}";
        var MEDICOID;
        $(document).ready(function(){
           
            showMedicos();

            $('#medico').change(function(){
                MEDICOID = $(this).val();
              
                mostrarHistorialIngresos(MEDICOID);
                showReporteHistirialGraficaEstadictico(MEDICOID);
            });
        });

        function mostrarHistorialIngresos(id_medico){
            TablaHistorial = $('#historial_ingresos').DataTable({
                bDestroy:true,
              "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": 0
                }],
                ajax:{
                    url:RUTA+"medico/reporte/historial/ingresos-detallado/"+id_medico,
                    method:"GET",
                    dataSrc:"response"
                },
                columns:[
                    {"data":"mes"},
                    {"data":"mes",render:function(mes){
                        return mes.toUpperCase()
                    }},
                    {"data":"monto",render:function(monto){
                        return `<span class='badge bg-success'><b class='text-primary'>`+monto+`</b></span>`;
                    }}
                ]
            });
/*=========================== ENUMERAR REGISTROS EN DATATABLE =========================*/
            TablaHistorial.on( 'order.dt search.dt', function () {
            TablaHistorial.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
            } );
            }).draw();
        }

        /// mostrar todos los médicos
        function showMedicos(){
            let option = '<option disabled selected>--- Seleccionar ----</option>';
            $.ajax({
                url:RUTA+"show/medicos",
                method:"GET",
                dataType:"json",
                success:function(response){
                   if(response.medicos.length > 0){
                     response.medicos.forEach(medico => {
                        option+=`<option value=`+medico.id_medico+`>`+(medico.apellidos+" "+medico.nombres).toUpperCase()+`</option>`;
                     });
                   }

                   $('#medico').html(option);
                }
            })
        }

        function showReporteHistirialGraficaEstadictico(idmedico) {
            let Data1 = [
                ['Element', 'Mes', {
                    role: 'style',
                }]
            ];
            $.ajax({
                url: RUTA + "medico/reporte/historial/ingresos-detallado/" + idmedico,
                method: "GET",
                success: function(response) {
                    response = JSON.parse(response);

                    if (response.response.length > 0) {
                        response.response.forEach(historia => {
                            Data1.push([historia.mes, parseInt(historia.monto), GenerateRgb()]);
                        });
                    } else {
                        Data1.push(["", 0, GenerateRgb()]);
                    }
                    GraficoBarraChart(Data1, 'Historial de ingresos del médico', 'Monto',
                        'reporte_historial')
                }
            })

        }
        /*Plnatilla reporte gráfico estadistico tipo barra*/
        function GraficoBarraChart(Data1 = [], TitleOptions, TitleOptions_2, IdDiv) {
            google.charts.load('current', {
                'packages': ['corechart']
            });
            google.charts.setOnLoadCallback(drawMultSeries);

            function drawMultSeries() {
                var data = new google.visualization.arrayToDataTable(Data1);

                var options = {
                    title: TitleOptions,

                    vAxis: {
                        title: TitleOptions_2
                    },
                    bar: {
                        groupWidth: "40%"
                    },

                };

                var chart = new google.visualization.ColumnChart(
                    document.getElementById(IdDiv));

                chart.draw(data, options);
            }
        }

          /** Generamos el rgb**/
          function GenerateRgb() {
            let Code = "(" + GenerateCodeAleatorio(255) + "," + GenerateCodeAleatorio(255) + "," + GenerateCodeAleatorio(
                255) + ")";
            return 'rgb' + Code;
        }
        /** Generamos código rgb aleatorios*/
        function GenerateCodeAleatorio(numero) {
            return (Math.random() * numero).toFixed(0);
        }
        
    </script>
@endsection