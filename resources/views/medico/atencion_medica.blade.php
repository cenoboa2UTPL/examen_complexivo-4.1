@extends($this->Layouts('dashboard'))

@section('title_dashboard', 'Atención médica')

@section('css')
    <style>
        #tabla_pacientes_atm>thead>tr>th {
            background-color: #1b5ab1;
            color:aliceblue;
            padding: 18px;
        }

        #tabla_receta>thead>tr>th {
            background-color: #1b5ab1;
            color: aliceblue;
            padding: 18px;
        }

        #div_table_ {
            overflow: scroll;
            height: 240px;
            width: 100%;
            border: 0.1rem solid #4169E1;
        }

        #div_table_ table {
            width: 100%;

        }

        #tabla_pacientes_atendidos>thead>tr>th {
            background-color: #1b5ab1;
            color:aliceblue;
            padding: 18px;
        }

        label {
            cursor: pointer;
        }

        input[type=radio] {
            width: 23px;
            height: 23px;
        }

        td.hide_me {
            display: none;
        }

        #lista_historial_clinico_paciente>thead>tr>th {
            background-color: #00205e;
            color: azure;
            padding: 20px;
        }

        #tabla_lista_evaluacion>thead>tr>th {
            background-color: #4169E1;
            color: azure;
            padding: 20px;
        }
    </style>
@endsection
@section('contenido')
    <div class="col-12" id="car">
        <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs nav-fill" role="tablist" id="tab_atencion_medico">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#plan_atencion" aria-controls="navs-justified-home" aria-selected="true"
                        style="color: #4169E1" id="plan_atencion_">
                        <i class='bx bxs-donate-heart'></i> Plan de atención médica

                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#pacientes_atendidos" aria-controls="navs-justified-profile" aria-selected="false"
                        style="color:#FF4500" id="pacientes_atendidos_">
                        <i class='bx bxs-user-detail'></i> Pacientes atendidos
                    </button>
                </li>

                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#historial_clinico" aria-controls="navs-justified-profile" aria-selected="false"
                        style="color:#20cba9" id="historial_clinico_">
                        <i class='bx bxs-file-archive'></i> Historial Clínico
                    </button>
                </li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="plan_atencion" role="tabpanel">
                    <div class="card-text p-3">
                        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-5 col-12">
                            <button class="btn_info_tw col-12"
                                id="pacientes_atencion_medica_fresh"><b>Refrescar <i class='bx bx-refresh'></i></b></button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped nowrap" id="tabla_pacientes_atm"
                                style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>PACIENTE</th>
                                        <th>HORA DE LA CITA</th>
                                        <th>CONSULTORIO</th>
                                        <th>ACCIÓN</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                    <div class="card-text p-3" style="display: none" id="form_paciente_atencion_medica">
                        <div class="row">

                            <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b># Documento</b></label>
                                    <input type="text" class="form-control" id="documento" readonly>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Paciente| Cliente</b></label>
                                    <input type="text" class="form-control" id="paciente" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-12">
                                <div class="form-group">
                                    <label for=""><b>Edad del paciente</b></label>
                                    <input type="text" class="form-control" id="edad" readonly>
                                </div>
                            </div>



                            <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Consultorio</b></label>
                                    <input type="text" class="form-control" id="consultorio_" readonly>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Especialista</b></label>
                                    <input type="text" class="form-control" id="especialista"
                                        value="{{ $this->profile()->apellidos }} {{ $this->profile()->nombres }}" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-12">
                                <div class="form-group">
                                    <label for=""><b>Presión arterial- mm Hg</b></label>
                                    <input type="text" class="form-control" id="pa" readonly>
                                </div>
                            </div>


                            <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Temperatura [°C]</b></label>
                                    <input type="text" class="form-control" id="temp" readonly>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Frecuencia cardiaca[T/minuto]</b></label>
                                    <input type="text" class="form-control" id="fc" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-12">
                                <div class="form-group">
                                    <label for=""><b>Frecuencia respiratoria[T/minuto]</b></label>
                                    <input type="text" class="form-control" id="frecart" readonly>
                                </div>
                            </div>


                            <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Saturación de oxigeno[%] </b></label>
                                    <input type="text" class="form-control" id="so" readonly>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Talla [Cm]</b></label>
                                    <input type="text" class="form-control" id="talla" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-12">
                                <div class="form-group">
                                    <label for=""><b>Peso[Kg]</b></label>
                                    <input type="text" class="form-control" id="peso" readonly>
                                </div>
                            </div>

                            <div class="col-xl-5 col-lg-5 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>IMC</b></label>
                                    <input type="text" class="form-control" id="imc" readonly>
                                </div>
                            </div>
                            <div class="col-xl-7 col-lg-7 col-md-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>ESTADO IMC</b></label>
                                    <input type="text" class="form-control" id="estadoimc" readonly>
                                </div>
                            </div>
                            {{-- - DATOS DE LA ATENCIÓN MÉDICA-- --}}
                            <div class="col-12">
                                <div class="form-group">
                                    <label for=""><b>Motivo de la consulta</b></label>
                                    <textarea name="motivo" id="motivo" cols="30" rows="4" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-8 col-md-7 col-12">
                                <div class="form-group">
                                    <label for=""><b>Antecedentes <span class="text-danger">(*)</span></b></label>
                                    <textarea name="antecedentes" id="antecedentes" cols="30" rows="3" class="form-control"
                                        placeholder="Describa los antecedentes del paciente..."></textarea>
                                </div>
                            </div>
                            {{-- -modificamos la vista desde este punto para el hosting-- --}}
                            <div class="col-xl-4 col-lg-4 col-md-5 col-12 mt-xl-3 mt-lg-3 mt-md-3 mt-0">
                                <div class="form-group">
                                    <label for=""><b>Tiempo de la enfermedad <span class="text-danger">(*)</span>
                                        </b></label>
                                    <input type="text" class="form-control" id="tiempo">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12 ">
                                <div class="form-group">
                                    <label for="antedcedente_medico"><b>Antecedentes médicos <span class="text-danger">
                                            </span> </b></label>
                                    <textarea name="antecedente_medico" id="antecedente_medico" cols="30" rows="2" class="form-control"
                                        placeholder="Describa los antecedentes médicos..."></textarea>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12  ">
                                <div class="form-group">
                                    <label for="antedcedente_traumas"><b>Antecedentes traumáticos <span
                                                class="text-danger"> </span> </b></label>
                                    <textarea name="antecedente_traumas" id="antecedente_traumas" cols="30" rows="2" class="form-control"
                                        placeholder="Describa los antecedentes traumáticos..."></textarea>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12 ">
                                <div class="form-group">
                                    <label for="antedcedente_gin_obs"><b>Antecedentes gineco obstetricos <span
                                                class="text-danger"> </span> </b></label>
                                    <textarea name="antecedente_gin_obs" id="antecedente_gin_obs" cols="30" rows="2" class="form-control"
                                        placeholder="Describa los antecedentes médicos..."></textarea>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12 ">
                                <div class="form-group">
                                    <label for="antedcedente_familiares"><b>Antecedentes familiares <span
                                                class="text-danger"> </span> </b></label>
                                    <textarea name="antecedente_familiares" id="antecedente_familiares" cols="30" rows="2"
                                        class="form-control" placeholder="Describa los antecedentes traumáticos..."></textarea>
                                </div>
                            </div>
                            <div class="card-text mt-3"><b class="text-primary">Hábitos</b></div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-12 mt-xl-3 mt-lg-3 mt-md-3 mt-0">
                                <div class="form-group">
                                    <label for="fuma"><b>Fuma ? <span class="text-danger"></span> </b></label>
                                    <select name="fuma" id="fuma" class="form-select">
                                        <option value="no">no</option>
                                        <option value="si">si</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12 mt-xl-3 mt-lg-3 mt-md-3 mt-0">
                                <div class="form-group">
                                    <label for="bebe"><b>Bebe ? <span class="text-danger"></span> </b></label>
                                    <select name="bebe" id="bebe" class="form-select">
                                        <option value="no">no</option>
                                        <option value="si">si</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 mt-xl-3 mt-lg-3 mt-md-3 mt-0">
                                <div class="form-group">
                                    <label for="opcional_habito"><b>Otros <span class="text-danger"></span> </b></label>
                                    <textarea name="opcional_habito" id="opcional_habito" cols="30" rows="2" class="form-control"
                                        placeholder="Describir si el paciente tiene otros hábitos (opcional)..."></textarea>
                                </div>
                            </div>

                            {{-- - HASTA AQUI-- --}}

                            <div class="col-xl-6 col-lg-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Alergías del paciente</span></b></label>
                                    <textarea name="alergias" id="alergias" cols="30" rows="4" class="form-control"
                                        placeholder="Describa las alergías del paciente..."></textarea>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-12">
                                <div class="form-group">
                                    <label for=""><b>Interveciones quirúrgicas </b></label>
                                    <textarea name="intervenciones" id="intervenciones" cols="30" rows="4" class="form-control"
                                        placeholder="Describa las intervenciones quirúgicas que tiene el paciente hasta la actualidad..."></textarea>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-3 col-12 mt-xl-5 mt-lg-5 mt-md-5 mt-0">
                                <div class="form-group">
                                    <label for=""><b>Vacunas completos ? <span
                                                class="text-danger">(*)</span></b></label>
                                    <select name="vacunas" id="vacunas" class="form-select">
                                        <option value="se">SIN ESPECIFICAR</option>
                                        <option value="si">Si</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-12">
                                <div class="form-group">
                                    <label for=""><b>Resultado del exámen físico <span
                                                class="text-danger">(*)</span></b></label>
                                    <textarea name="examen_fisico" id="examen_fisico" cols="30" rows="6" class="form-control"
                                        placeholder="Describa los resultados del exámen físico del paciente..."></textarea>
                                </div>
                            </div>
                            <div class="card-text"><b>Enfermedad diagnosticado </b></div>
                            <div class="col-12">
                                <textarea name="diagnostico_medico_" id="diagnostico_medico_" cols="30" rows="6" class="form-control"
                                    placeholder="Escriba la enfermedad diagnosticado...."></textarea>

                            </div>
                            <div class="col-xl-3 col-lg-3 col-12 mt-xl-5 mt-lg-5 mt-md-5 mt-0">
                                <div class="form-group">
                                    <label for=""><b>Requiere análisis <span
                                                class="text-danger">(*)</span></b></label>
                                    <select name="requiere_analisis" id="requiere_analisis" class="form-select">
                                        <option value="no">No</option>
                                        <option value="si">Si</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-12">
                                <div class="form-group">
                                    <label for=""><b>Describir análisis que debe de sacarse el paciente</b></label>
                                    <textarea name="analisis" id="analisis" cols="30" rows="6" class="form-control"
                                        placeholder="Describa que análisis requiere el paciente..." readonly></textarea>
                                </div>
                            </div>

                            <div class="card-text"><b>Tratamiento</b></div>
                            <div class="col-xl-3 col-lg-3 col-12 mt-xl-5 mt-lg-5 mt-md-5 mt-0">
                                <div class="form-group">
                                    <label for=""><b>Tiempo de tratamiento</b></label>
                                    <input type="text" id="tiempo_tratamiento" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-12">
                                <div class="form-group">
                                    <label for=""><b>Describir el tratamiento</b></label>
                                    <textarea name="tratamiento" id="tratamiento" cols="30" rows="6" class="form-control"
                                        placeholder="Describa el tratamiento para el paciente..."></textarea>
                                </div>
                            </div>

                            <div class="card-text"><b>Crear receta para el paciente</b></div>
                            <div class="col-12">
                                <button class="btn_3d" id="nueva_receta"><i class='bx bx-plus'></i> Agregar</button>
                                <div class="table-responsive" id="div_table_">
                                    <table class="table table-bordered mt-1" id="tabla_receta">
                                        <thead>
                                            <tr>
                                                <th>Medicamento</th>
                                                <th class="col-10">Dosis</th>
                                                <th>Duración</th>
                                                <th>Cantidad</th>
                                                <th>Quitar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lista_receta">

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <b>Indicar la próxima cita </b>
                                    <input type="date" class="form-control" id="fecha_proxima_cita"
                                        value="{{ $this->addRestFecha('Y-m-d', '+ 7 day') }}"
                                        min="{{ $this->addRestFecha('Y-m-d', '+ 7 day') }}">
                                </div>
                            </div>

                            <div class="col p-3">
                                <button class="btn btn-rouded btn-success" id="save_paciente_plan_atencion">Guardar <i
                                        class='bx bx-save'></i></button>
                                <button class="btn btn-rounded btn-danger" id="cancel">Cancelar <i
                                        class='bx bx-x'></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pacientes_atendidos" role="tabpanel">
                    <div class="row mt-2">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="diario" class="form-label"><input type="radio" id="diario"
                                        name="opcion" checked><b> Pacientes de hoy</b></label>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="ayer" class="form-label"><input type="radio" id="ayer"
                                        name="opcion"><b> Pacientes de Ayer</b></label>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="semana_pasada" class="form-label"><input type="radio" id="semana_pasada"
                                        name="opcion"><b> Pacientes de esta semana</b></label>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-5 col-12">
                            <div class="form-group">
                                <label for="mes_pasado" class="form-label"><input type="radio" id="mes_pasado"
                                        name="opcion"><b> Pacientes de este mes</b></label>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-5 col-12">
                            <div class="form-group">
                                <label for="fecha_atencion_medica" class="form-label"><input type="radio"
                                        id="fecha_atencion_medica" name="opcion"><b> Fecha personalizado</b></label>
                            </div>
                        </div>

                        <div class="col-12" id="fecha_select" style="display:none">
                            <div class="form-group">
                                <label for=""><b>Seleccione una fecha</b></label>
                                <input type="date" id="fecha_atencion" class="form-control"
                                    value="{{ $this->FechaActual('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="table table-responsive">
                        <table class="table table-bordered responsive table-striped table-hover nowrap responsive"
                            id="tabla_pacientes_atendidos" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th># DOCUMENTO</th>
                                    <th>PACIENTE</th>
                                    <th>FECHA</th>
                                    <th>SERVICIO</th>
                                    <th>PRÓXIMA CITA</th>
                                    <th class="d-none">ID ATENCION</th>
                                    <th>ACCIÓN</th>
                                </tr>
                            </thead>
                        </table>

                    </div>

                    <div class="row justify-content-end">
                        <div class="col-xl-4 col-lg-5 col-md-6 col-sm-7 col-12">
                            <table class="table table-bordered">
                                <thead style="background-color: #FF4500">
                                    <th colspan="2" class="text-white">Leyenda</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ver receta médica</td>
                                        <td><button class="btn rounded btn-warning btn-sm"><i
                                                    class='bx bxs-file-blank'></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Ver órdern de laboratorio</td>
                                        <td><button class="btn rounded btn-danger btn-sm"> <i
                                                    class='bx bx-file'></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Crear informe médico</td>
                                        <td> <button class="btn rounded btn-info btn-sm text-white"><i
                                                    class='bx bxs-file-blank'></i> <b></b></button></td>
                                    </tr>

                                    <tr>
                                        <td>Crear órden de laboratorio</td>
                                        <td>
                                            <button class="btn rounded btn-success btn-sm text-white"> <i
                                                    class='bx bx-street-view'></i> <b></b></button>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="historial_clinico" role="tabpanel">
                    <div class="row">
                        <div class="col">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                    placeholder="Buscar por # documento del paciente.." id="doc_paciente">
                                <button class="btn btn-outline-primary btn-rounded" id="search_pacientes_medico"><i
                                        class='bx bx-search'></i> Buscar</button>
                            </div>
                            <span class="text-danger mt-1" style="display: none"
                                id="error_doc_paciente_historial">Complete por lo menos 8 dígitos 😁</span>
                        </div>

                        <div class="col-12">
                            <div class="table table-responsive">
                                <table class="table table-bordered table-striped nowrap responsive"
                                    id="lista_historial_clinico_paciente" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>PACIENTE</th>
                                            <th>FECHA DE LA CITA</th>
                                            <th>ESPECIALIDAD</th>
                                            <th>HISTORIAL</th>
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



    <div class="modal fade" id="detalle_receta" data-bs-backdrop="static">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #1b5ab1;color:">
                    <h4 class="modal-start text-white">Receta médica</h4>
                    <button type="button" class="btn-close salir_receta" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col" id="alerta_existe_medicamento_receta" style="display: none">
                        <div class="alert alert-danger">
                            <b>Ya existe ese medicamento en la lista</b>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="medicamento">MEDICAMENTO <span class="text-danger">(*)</span></label>
                        <input type="text" id="medicamento" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="dosis">FRECUENCIA <span class="text-danger">(*)</span></label>
                        <textarea name="dosis" id="dosis" cols="30" rows="4" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="duracion_">DURACIÓN<span class="text-danger">(*)</span></label>
                        <input type="text" id="duracion_" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="cantidad">CANTIDAD <span class="text-danger">(*)</span></label>
                        <input type="number" id="cantidad" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- -MODAL PARA GENERAR EL INFORME MÉDICO DEL PACIENTE ATENDIDO -- --}}

    <div class="modal fade" id="modal_informe_medico" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg bg-primary">
                    <p class="h4 text-white"><span id="text_informe">Generar informe médico</span></p>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="paciente_informe"><b>Paciente</b></label>
                        <input type="text" id="paciente_informe" class="form-control" disabled>
                    </div>

                    <div class="form-group">
                        <label for="doc_paciente_informe"><b>N° - DOCUMENTO </b></label>
                        <input type="text" id="doc_paciente_informe" class="form-control" disabled>
                    </div>

                    <div class="form-group">
                        <label for="detalle_informe"><b>Descripción del informe <span
                                    class="text-danger">(*)</span></b></label>
                        <textarea name="detalle_informe" id="detalle_informe" cols="30" rows="10" class="form-control"
                            placeholder="Escriba aquí......." style="border: #4169E1 solid 1px"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-rounded btn-success" id="save_informe"><b>Generar <i
                                class='bx bx-save'></i></b></button>
                    <button class="btn btn-rounded btn-danger" id="cancel_informe"><b>Cancelar <i
                                class='bx bx-save'></i></b></button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PARA MOSTRAR A LOS PACIENTES QUE YA HAN SIDO ATENDIDOS PARA VER SU HISTORIAL CLINICO-- --}}
    <div class="modal fade" id="modal-pacientes_medico" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-body">
                    <div class="card-text">
                        <h4>Mis pacientes</h4>

                    </div>
                    <div class="table table-responsive">
                        <table class="table table-bordered table-striped nowrap responsive" id="lista_pacientes_medico_"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th># DOCUMENTO</th>
                                    <th>PACIENTE</th>
                                    <th>SELECCIONAR</th>
                                </tr>
                            </thead>

                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger" onclick="$('#modal-pacientes_medico').modal('hide')"><i
                            class='bx bx-x'></i> Salir</button>
                </div>
            </div>
        </div>
    </div>

    {{-- - MODAL PARA EL REGISTRO DE LA EVALUACIÓN PRE-OPERATORIA- --}}
    <div class="modal fade" id="modal_evaluacion_pre_operatoria">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header badge bg-primary">
                    <h5 class="text-white">Órden de laboratorio</h5>
                    <div class="float-end">
                        <img src="{{$this->asset('img/icons/unicons/laboratorio.ico')}}" class="menu-icon" alt="">
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-7 col-lg-7 col-12">
                            <div class="form-group">
                                <label for="paciente_lab_update"><b>Paciente</b></label>
                                <input type="text" id="paciente_lab_update" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5 col-12">
                            <div class="form-group">
                                <label for="fecha_atencion_lab_update"><b>Fecha de atención</b></label>
                                <input type="text" id="fecha_atencion_lab_update" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="orden_lab_update"><b>Esribir órden de laboratorio</b></label>
                                <textarea name="orden_lab_update" id="orden_lab_update" cols="30" rows="10" class="form-control"
                                    placeholder="Escriba aquí...."></textarea>
                            </div>
                        </div>
                         
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-success rounded" id="save_evaluacion"><b>Generar <i
                   class='bx bx-save'></i></b></button>
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
        var USER = "{{ $this->profile()->id_usuario }}";
        var ALGORITMO = "{{ env('ALGORITMO') }}";
        var KEY_ENCRIPT = "{{ env('CLAVE_ENCRYPT') }}";
        var CITA_ID = null;
        var HORARIO_ID = null;
        var TRIAJE_ID = null;
        var PACIENTE_ATENDIDO_ID;
        var INFORME_ID = null;
        var ATENCION_ID_OPERATORIA;
        var Control_Save_Informe = 'save';
        var tabla_receta = document.getElementById('lista_receta');

        var TablaPacientesAtencionMedica;
        var TablaPacientesAtendidos;
        var TablaHistorialClinico;
        var PacientesDelMedico;
        var TablaListaEvaluacion;
        $(document).ready(function() {
            /*DATOS PARA LA ATENCION MÉDICA*/
            let Antecedente = $('#antecedentes');
            let TiempoEnfermedad = $('#tiempo');
            let Alergias = $('#alergias');
            let Intervenciones = $('#intervenciones');
            let Vacunas = $('#vacunas');
            let ExamenFisico = $('#examen_fisico');
            let Diagnostico = $('#diagnostico_medico_');
            let AnalisisConfirm = $('#requiere_analisis');
            let Analisis = $('#analisis');
            let TiempoTratamiento = $('#tiempo_tratamiento');
            let Tratamiento = $('#tratamiento');
            let MotivoConsulta = $('#motivo');
            let LaProximaCitaPaciente = $('#fecha_proxima_cita');
            let DetalleInforme = $('#detalle_informe');
            let PacienteInforme = $('#paciente_informe');

            /// variables a modificar en el hosting
            let Ant_medicos_ = $('#antecedente_medico');
            let Ant_trauma_ = $('#antecedente_traumas');
            let Ant_gineco_obs_ = $('#antecedente_gin_obs');
            let Ant_fam_ = $('#antecedente_familiares');
            let Fuma_ = $('#fuma');
            let Bebe_ = $('#bebe');
            let Otros_ = $('#opcional_habito');
            /*** DATOS PARA LA EVALUACION**/
            let Ev_Indicaciones = $('#indicaciones');
            let Ev_ant_importantes = $('#ant_importantes');
            let Ev_molestias_importantes = $('#molestias_importantes');
            let Ev_Pa = $('#pa');
            let Ev_fcc = $('#fcc');
            let Ev_Fr = $('#fr');
            let Ev_To = $('#to');
            let Ev_sato_dos = $('#sato_dos');
            let Ev_Peso = $('#peso');
            let Ev_exa_fisico = $('#exa_fisico');
            let Ev_resultados_est = $('#resultados_est');
            let Ev_goldman = $('#goldman');
            let Ev_Asa = $('#asa');
            let Ev_Sugerencias = $('#sugerencias');

            showPacientesEnAtencionMedica();
            atencionMedica(TablaPacientesAtencionMedica, '#tabla_pacientes_atm tbody');
            /// control de los navs
            $('#tab_atencion_medico button').on('click', function(evento) {
                evento.preventDefault();

                /// asignamos nuevo atributo a los botones de guardar y cancelar

                Control = $(this)[0].id;

                if (Control === 'pacientes_atendidos_') {
                    pacientesAtendidos(1, "2023-08-20");
                    $('#diario').prop("checked", true);
                    GenerarInforme(TablaPacientesAtendidos, '#tabla_pacientes_atendidos tbody');
                    GenerarOrdenLaboratorio(TablaPacientesAtendidos, '#tabla_pacientes_atendidos tbody');
                    OpenModalEvaluacionPreOperatoria(TablaPacientesAtendidos,
                        '#tabla_pacientes_atendidos tbody')
                } else {
                    if (Control === 'historial_clinico_') {
                        $('#doc_paciente').val("");
                        $('#doc_paciente').attr('disabled', false);
                        $('#doc_paciente').focus();
                        MostrarHistorialPaciente("xxxxxxxx");
                    }
                }


                $(this).tab("show");
                MostrarMedicosEspecialidades_()
            });

            $('#cancel').click(function() {
                $('#form_paciente_atencion_medica').hide(700);
                $('#lista_receta tr').empty();
            });
           
            /// consultar el historial clínico del paciente
            $('#doc_paciente').keypress(function(evento) {
                if (evento.which === 13) {
                    if ($(this).val().trim().length >= 8) {
                        $('#error_doc_paciente_historial').hide();
                        $(this).removeClass("is-invalid");
                        $(this).addClass('is-valid');

                        MostrarHistorialPaciente($(this).val());
                    } else {
                        $(this).removeClass('is-valid');
                        $(this).addClass('is-invalid');
                        $('#error_doc_paciente_historial').show(270);
                    }
                }
            });

            /// cancelar informe médico
            $('#cancel_informe').click(function() {

                DetalleInforme.val("")
                $('#modal_informe_medico').modal("hide");

                Control_Save_Informe = 'save';
            });

            /// mostramos el modal de pacientes del médico
            $('#search_pacientes_medico').click(function() {
                $('#modal-pacientes_medico').modal('show')
                ShowPacientes_();
                SelectPaciente(PacientesDelMedico, '#lista_pacientes_medico_ tbody')
            });

            /// Guardar el informe médico del paciente

            $('#save_informe').click(function() {
                if (DetalleInforme.val().trim().length == 0) {
                    DetalleInforme.focus();
                    Swal.fire({
                        title: 'Mensaje del sistema!',
                        text: 'Ingrese la descripción del informe',
                        icon: 'error',
                        target: document.getElementById('modal_informe_medico')
                    });
                } else {
                    if (Control_Save_Informe === 'save') {
                        $.ajax({
                            url: RUTA + "informe_medico/" + PACIENTE_ATENDIDO_ID + "/save",
                            method: "POST",
                            data: {
                                token_: TOKEN,
                                detalle_informe: DetalleInforme.val()
                            },
                            success: function(response) {
                                response = JSON.parse(response);

                                if (response.response === 'ok') {
                                    Swal.fire({
                                        title: 'Mensaje del sistema!',
                                        text: 'Informe médico del paciente ' +
                                            PacienteInforme +
                                            ' se a registrado correctamente',
                                        icon: 'success',
                                        target: document.getElementById(
                                            'modal_informe_medico')
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Mensaje del sistema!',
                                        text: 'Error al registrar el informe médico del paciente ' +
                                            PacienteInforme,
                                        icon: 'success',
                                        target: document.getElementById(
                                            'modal_informe_medico')
                                    });
                                }
                            }
                        })
                    } else {
                        actualizarInforme(INFORME_ID);
                    }
                }


            });

            $('#save_evaluacion').click(function() {
                updateOrdenLaboratorio_(ATENCION_ID_OPERATORIA,$('#orden_lab_update').val());
            });

            $('#nueva_receta').click(function() {

                $('#medicamento').focus();
                $('#detalle_receta').modal('show');
            });



            $('.salir_receta').click(function() {
                $('#medicamento').val("");
                $('#dosis').val("");
                $('#duracion_').val("");
                $('#cantidad').val("");
            });

            $('#diagnostico_search').click(function() {

            });

            /// reporte de atencion médica
            $('#diario').click(function() {
                $('#fecha_select').hide(600);
                pacientesAtendidos(1, "2023-08-20");
                GenerarInforme(TablaPacientesAtendidos, '#tabla_pacientes_atendidos tbody');
            });

            /// pacienets en atención médica (REFRESH)
            $('#pacientes_atencion_medica_fresh').click(function() {
                showPacientesEnAtencionMedica();
            });

            $('#ayer').click(function() {
                $('#fecha_select').hide(600);
                pacientesAtendidos(2, "2023-08-20");
                GenerarInforme(TablaPacientesAtendidos, '#tabla_pacientes_atendidos tbody');
            })

            $('#semana_pasada').click(function() {
                $('#fecha_select').hide(600);
                pacientesAtendidos(3, "2023-08-20");
                GenerarInforme(TablaPacientesAtendidos, '#tabla_pacientes_atendidos tbody');
            })

            $('#mes_pasado').click(function() {
                $('#fecha_select').hide(600);
                pacientesAtendidos(4, "2023-08-20");
                GenerarInforme(TablaPacientesAtendidos, '#tabla_pacientes_atendidos tbody');
            })

            $('#fecha_atencion_medica').click(function() {
                $('#fecha_select').show(600);
                pacientesAtendidos(5, $('#fecha_atencion').val());
                GenerarInforme(TablaPacientesAtendidos, '#tabla_pacientes_atendidos tbody');
            })

            $('#fecha_atencion').change(function() {
                pacientesAtendidos(5, $(this).val());
            })

            AnalisisConfirm.change(function() {
                if ($(this).val().trim() === 'si') {
                    Analisis.removeAttr('readonly');
                    Analisis.focus();
                } else {
                    Analisis.val("");
                    Analisis.attr('readonly', 'readonly');
                }
            });

            $('#save_paciente_plan_atencion').click(function() {

                if (Antecedente.val().trim().length == 0) {
                    Antecedente.focus();
                } else {
                    if (TiempoEnfermedad.val().trim().length == 0) {
                        TiempoEnfermedad.focus();
                    } else {
                        if (ExamenFisico.val().trim().length == 0) {
                            ExamenFisico.focus();
                        } else {
                            if (tabla_receta.rows.length == 0) {
                                saveAtencionMedicaPaciente(
                                    Antecedente, TiempoEnfermedad, Alergias, Intervenciones, Vacunas,
                                    ExamenFisico, Diagnostico, AnalisisConfirm, Analisis,
                                    TiempoTratamiento, Tratamiento, TRIAJE_ID, CITA_ID, HORARIO_ID,
                                    MotivoConsulta, LaProximaCitaPaciente,
                                    Ant_medicos_, Ant_trauma_, Ant_gineco_obs_, Ant_fam_, Fuma_, Bebe_,
                                    Otros_
                                );
                            } else {

                                saveAtencionMedicaPaciente(
                                    Antecedente, TiempoEnfermedad, Alergias, Intervenciones, Vacunas,
                                    ExamenFisico, Diagnostico, AnalisisConfirm, Analisis,
                                    TiempoTratamiento, Tratamiento, TRIAJE_ID, CITA_ID, HORARIO_ID,
                                    MotivoConsulta, LaProximaCitaPaciente,
                                    Ant_medicos_, Ant_trauma_, Ant_gineco_obs_, Ant_fam_, Fuma_, Bebe_,
                                    Otros_
                                );
                                saveRecetaMedicaPaciente();
                            }
                        }
                    }
                }
            });

            // pasar enter
            enter('medicamento', 'dosis');
            enter('duracion_', 'cantidad');

            $('#cantidad').keypress(function(evento) {
                if (evento.which == 13) {
                    evento.preventDefault();

                    if ($(this).val().trim().length == 0) {
                        $(this).focus();
                    } else {
                        if (!existeReceta($('#medicamento').val())) {
                            let tr = '';

                            tr += `
                        <tr>
                            <td>` + $('#medicamento').val() + `</td>
                            <td>` + $('#dosis').val() + `</td>
                            <td>` + $('#duracion_').val() + `</td>
                            <td>` + $('#cantidad').val() + `</td>
                            <td><button class="btn btn-rounded btn-outline-danger btn-sm" id="quitar"><i class='bx bx-x'></i></button></td>
                        </tr>
                        `;

                            $('#lista_receta').append(tr);
                            $('#medicamento').val("");
                            $('#dosis').val("");
                            $('#duracion_').val("");
                            $('#cantidad').val("");

                            $('#medicamento').focus();
                            $('#alerta_existe_medicamento_receta').hide(200);
                        } else {
                            $('#alerta_existe_medicamento_receta').show(600);
                            $('#medicamento').select();
                        }
                    }
                }
            });

            $('#lista_receta').on('click', '#quitar', function() {

                let fila = $(this).closest('tr');

                // quitamos de la lista

                fila.remove();
            });
        });

        function existeReceta(data) {
            let bandera = false;

            for (let i = 0; i < tabla_receta.rows.length; i++) {
                if (tabla_receta.rows[i].cells[0].innerHTML === data) {
                    bandera = true;
                }
            }

            return bandera;
        }

        /// guardar atenciónn medica del paciente que sacó la cita
        function saveAtencionMedicaPaciente(
            antecedente, tiempo_enfermedad, alergias, interv_quir, vacuna,
            examen_fisico, diagnostico, analisisConfirm, desc_analisis,
            plan_tratamiento, desc_tratamiento, triaje_, citamedica, horario, observacion_, Proxima_cita_paciente,
            ant_medicos_, ant_trauma_, ant_gineco_obs_, ant_fam_, fuma_, bebe_, otros_
        ) {
            $.ajax({
                url: RUTA + "save_atencion_medica_paciente",
                method: "POST",
                async: false,
                data: {
                    token_: TOKEN,
                    antecedente: antecedente.val(),
                    tiempo_enfermedad: tiempo_enfermedad.val(),
                    alergias: alergias.val(),
                    interv_quir: interv_quir.val(),
                    vacuna: vacuna.val(),
                    examen_fisico: examen_fisico.val(),
                    diagnostico: diagnostico.val(),
                    analisisConfirm: analisisConfirm.val(),
                    desc_analisis: desc_analisis.val(),
                    plan_tratamiento: plan_tratamiento.val(),
                    desc_tratamiento: desc_tratamiento.val(),
                    triaje: triaje_,
                    proxima_cita: Proxima_cita_paciente.val(),
                    cita_medica: citamedica,
                    obs: observacion_.val(),
                    horario_id: horario,
                    ant_medicos: ant_medicos_.val(),
                    ant_trauma: ant_trauma_.val(),
                    ant_gineco_obs: ant_gineco_obs_.val(),
                    ant_fam: ant_fam_.val(),
                    fuma: fuma_.val(),
                    bebe: bebe_.val(),
                    otros: otros_.val()
                },
                success: function(response) {
                    response = JSON.parse(response);

                    if (response.response === 'ok') {
                        Swal.fire({
                            title: "Mensaje del sistema!",
                            text: "La atención médica del paciente " + $('#paciente').val() +
                                " se ha registrado correctamente",
                            icon: "success"
                        }).then(function() {
                            /// receteamos todo el formulario
                            showPacientesEnAtencionMedica();
                            antecedente.val("");
                            tiempo_enfermedad.val("");
                            alergias.val("");
                            interv_quir.val("");
                            vacuna.val("se");
                            examen_fisico.val("");
                            diagnostico.val("");
                            analisisConfirm.val("no");
                            desc_analisis.val("");
                            plan_tratamiento.val("");
                            desc_tratamiento.val("");
                            observacion_.val("");
                            $('#lista_receta tr').empty();
                            TRIAJE_ID = null;
                            CITA_ID = null;
                            HORARIO_ID = null;
                            $('#form_paciente_atencion_medica').hide(500);

                        });
                    }
                }
            });
        }



        /// guardar la receta del paciente
        function saveRecetaMedicaPaciente() {
            $('#lista_receta tr').each(function() {

                let medicamento = $(this).find('td').eq(0).text();
                let Dosis = $(this).find('td').eq(1).text();
                let TiempoToma = $(this).find('td').eq(2).text();
                let Cantidad_ = $(this).find('td').eq(3).text();

                $.ajax({
                    url: RUTA + "save_receta_paciente",
                    method: "POST",
                    data: {
                        token_: TOKEN,
                        triaje_id: TRIAJE_ID,
                        medic: medicamento,
                        dosis: Dosis,
                        tiempo_dosis: TiempoToma,
                        cantidad: Cantidad_
                    },
                    success: function(response) {}
                });

            });
        }
        /// mostrar pacientes que pasan a la atención médica
        function showPacientesEnAtencionMedica() {
            TablaPacientesAtencionMedica = $('#tabla_pacientes_atm').DataTable({
                retrieve: true,
                responsive: true,
                language: SpanishDataTable(),
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                }],
                "order": [
                    [2, 'asc']
                ], /// enumera indice de las columnas de Datatable
                ajax: {
                    url: RUTA + "pacientes/cola/atencion_medica?token_=" + TOKEN + "&&medico=" + USER,
                    method: "GET",
                    dataSrc: "response"
                },
                columns: [{
                        "data": "paciente_atencion"
                    },
                    {
                        "data": "paciente_atencion"
                    },
                    {
                        "data": "hora_de_la_cita",
                        render: function(hora) {
                            return '<span class="badge bg-success text-primary">' + hora + '</span>';
                        }
                    },
                    {
                        "data": "nombre_esp",
                        render: function(especialida) {
                            return '<b>' + especialida + '</b>';
                        }
                    },
                    {
                        "data": null,
                        render: function() {
                            return '<button class="btn btn-rounded btn-outline-primary btn-sm" id="atencion"><i class="bx bxs-user-check"></i></button>';
                        }
                    },
                ]

            }).ajax.reload();
            /*=========================== ENUMERAR REGISTROS EN DATATABLE =========================*/
            TablaPacientesAtencionMedica.on('order.dt search.dt', function() {
                TablaPacientesAtencionMedica.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        }
        /// mostrar pacientes atendidos
        function pacientesAtendidos(opcion, fecha) {
            TablaPacientesAtendidos = $('#tabla_pacientes_atendidos').DataTable({
                bDestroy: true,
                responsive: true,
                processing: true,
                language: SpanishDataTable(),
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                }],
                "order": [
                    [1, 'asc']
                ], /// enumera indice de las columnas de Datatable
                ajax: {
                    url: RUTA + "atencion_medica/pacientes_atendidos/" + opcion + "/" + fecha + "?token_=" + TOKEN,
                    method: "GET",
                    dataSrc: "response",
                },
                columns: [{
                        "data": "documento"
                    },
                    {
                        "data": "documento"
                    },
                    {
                        "data": "paciente_atencion"
                    },
                    {
                        "data": "fecha_de_la_cita"
                    },
                    {
                        "data": "nombre_esp"
                    },
                    {
                        "data": "prox_cita"
                    },
                    {
                        "data": "id_atencion_medica"
                    },
                    {
                        "data": null,
                        render: function(dta) {
                            return `
           <div class="row">
               <div class="col-xl-5 col-lg-5 col-md-6 col-6">
                    <button class="btn rounded btn-success btn-sm text-white" id="ev_pre_operatoria"> <i class='bx bx-street-view'></i> <b></b></button>
                </div>
                <div class="col-xl-5 col-lg-5 col-md-6   col-6">
                    <a class="btn rounded btn-warning btn-sm" href='` + RUTA + `receta_medica?v=` + dta
                                .id_atencion_medica + `' target='blank_' id="receta_pdf"><i class='bx bxs-file-blank'></i></a>
                </div>
            
                <div class="col-xl-5 col-lg-5 col-md-6  col-6">
                    <button class="btn rounded btn-danger btn-sm" id="laboratorio"><i class='bx bx-file'></i></button>
                </div>
                <div class="col-xl-5 col-lg-5 col-md-6  col-6">
                    <button class="btn rounded btn-info btn-sm text-white" id="informe_medico"><i class='bx bxs-file-blank'></i> <b></b></button>
                </div>
            </div>
            `
                        }
                    }
                ],
                columnDefs: [{
                    "sClass": "hide_me",
                    target: 6
                }]
            });

            /*=========================== ENUMERAR REGISTROS EN DATATABLE =========================*/
            TablaPacientesAtendidos.on('order.dt search.dt', function() {
                TablaPacientesAtendidos.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        }

        function atencionMedica(Tabla, Tbody) {
            $(Tbody).on('click', '#atencion', function() {

                /// abrimos el aformulario de atención médica
                BajadaScroll('.modal-body,html', 400);
                $('#form_paciente_atencion_medica').show(800);

                let fila = $(this).parents('tr');

                if (fila.hasClass('child')) {
                    fila = fila.prev();
                }

                let Data = Tabla.row(fila).data();

                TRIAJE_ID = Data.id_triaje;

                $('#documento').val(Data.documento);
                $('#paciente').val(Data.paciente_atencion);

                /// obtenemos la fecha de nacimiento del paciente 1996-11-06
                let FechaNacimiento = Data.fecha_nacimiento;

                if (FechaNacimiento != null) {
                    AnioNacimiento = parseInt(FechaNacimiento.substr(0, 4));
                    MesNacimiento = parseInt(FechaNacimiento.substr(5, 2));
                    DiaNacimiento = parseInt(FechaNacimiento.substr(8, 2));

                    let date_ = new Date();

                    let AnioActual = date_.getFullYear();
                    let MesActual = date_.getMonth() + 1;
                    let DiaActual = date_.getDate();

                    let Edad = AnioActual - AnioNacimiento;

                    if (MesNacimiento === MesActual && DiaNacimiento === DiaActual) {
                        $('#edad').val(Edad + " años cumplidos");
                    } else {
                        $('#edad').val("Por cumplir " + Edad + " años aprox");
                    }

                    $('#edad').css({
                        'background-color': '#eceef1',
                        'opacity': '1'
                    });
                } else {
                    $('#edad').val('Fecha nacimiento no específicado');
                    $('#edad').css({
                        'background-color': 'red',
                        'color': 'white'
                    });
                }

                $('#motivo').val(Data.observacion);
                $('#consultorio_').val(Data.nombre_esp);
                $('#pa').val(Data.presion_arterial == null ? '------------------' : Data.presion_arterial);
                $('#temp').val(Data.temperatura == null ? '------------------' : Data.temperatura);
                $('#fc').val(Data.frecuencia_cardiaca == null ? '------------------' : Data.frecuencia_cardiaca);
                $('#frecart').val(Data.frecuencia_respiratoria == null ? '------------------' : Data
                    .frecuencia_respiratoria);
                $('#so').val(Data.saturacion_oxigeno == null ? '------------------' : Data.saturacion_oxigeno);
                $('#talla').val(Data.talla == null ? '------------------' : Data.talla);
                $('#peso').val(Data.peso == null ? '------------------' : Data.peso);
                $('#imc').val(Data.imc == null ? '------------------' : Data.imc);
                $('#estadoimc').val(Data.estado_imc == null ? '------------------' : Data.estado_imc);
                $('#motivo').focus();

                CITA_ID = Data.id_cita_medica;
                HORARIO_ID = Data.id_horario;


            });
        }

        /// generar informe médico del paciente
        function GenerarInforme(Tabla, Tbody) {
            $(Tbody).on('click', '#informe_medico', function() {
                /// fila seleccionada
                let Fila = $(this).parents("tr");

                if (Fila.hasClass("child")) {
                    Fila = Fila.prev();
                }

                let Paciente_Seleccionado = Fila.find('td').eq(2).text();
                let DocumentoPaciente = Fila.find('td').eq(1).text();
                PACIENTE_ATENDIDO_ID = Fila.find('td').eq(6).text();
                $('#paciente_informe').val(Paciente_Seleccionado.toUpperCase());
                $('#doc_paciente_informe').val(DocumentoPaciente);
                if (existeInforme(PACIENTE_ATENDIDO_ID).length > 0) {
                    $('#text_informe').text("Editar informe médico");
                    loading('body', '#4169E1', 'chasingDots')
                    setTimeout(() => {
                        $('#modal_informe_medico').modal("show");
                        Control_Save_Informe = 'update';
                        INFORME_ID = existeInforme(PACIENTE_ATENDIDO_ID)[0].id_informe;
                        $('#detalle_informe').val(existeInforme(PACIENTE_ATENDIDO_ID)[0]
                            .descripcion_medica);
                        $('body').loadingModal('hide');
                        $('body').loadingModal('destroy');
                    }, 1000);
                    return;
                }
                $('#text_informe').text("Generar informe médico")
                $('#modal_informe_medico').modal("show");
                Control_Save_Informe = 'save';
            });
        }
        /// generar datos de la órden del laboratorio en pdf
        function GenerarOrdenLaboratorio(Tabla, Tbody) {
            $(Tbody).on('click', '#laboratorio', function() {
                /// fila seleccionada
                let Fila = $(this).parents("tr");

                if (Fila.hasClass("child")) {
                    Fila = Fila.prev();
                }
                let DataAtencionId = Fila.find('td').eq(6).text();
                /// CONTINUAR LA ORDEN DEL LABORATORIO EN PDF AQUÍ
                window.open("{{ $this->route('paciente/orden_laboratorio/') }}" + DataAtencionId, 'blank_')
            });
        }

        // Modal Para registrar la evaluación pre operatoria de un paciente que a sido atendido
        function OpenModalEvaluacionPreOperatoria(Tabla, Tbody) {

            $(Tbody).on('click', '#ev_pre_operatoria', function() {
                /// fila seleccionada
                let Fila = $(this).parents("tr");

                if (Fila.hasClass("child")) {
                    Fila = Fila.prev();
                }
                let PacienteEvaluacion = Fila.find('td').eq(2).text();
                let FechaEvaluacion = Fila.find('td').eq(3).text();

                ATENCION_ID_OPERATORIA = Fila.find('td').eq(6).text();
 
               // $('#modal_evaluacion_pre_operatoria').modal("show");
               $('#paciente_lab_update').val(PacienteEvaluacion);
               $('#fecha_atencion_lab_update').val(FechaEvaluacion);
               editarOrdenLaboratorio(ATENCION_ID_OPERATORIA);
            });
        }


        /// verificamos la existencia del informe
        function existeInforme(id) {
            let respuesta = show(RUTA + "informe_medico/" + id + "/verificar_existencia?token_=" + TOKEN);

            return respuesta;

        }

        function actualizarInforme(id) {
            $.ajax({
                url: RUTA + "informe_medico/" + id + "/update",
                method: "POST",
                data: {
                    token_: TOKEN,
                    detalle_informe: $('#detalle_informe').val()
                },
                success: function(response) {
                    response = JSON.parse(response);

                    if (response.response === 'ok') {
                        Swal.fire({
                            title: 'Mensaje del sistema!',
                            text: 'Informe médico del paciente ' + $('#paciente_informe').val() +
                                ' se a modificado correctamente',
                            icon: 'success',
                            target: document.getElementById('modal_informe_medico')
                        });
                    } else {
                        Swal.fire({
                            title: 'Mensaje del sistema!',
                            text: 'Error al modificar el informe médico del paciente ' + $(
                                '#paciente_informe').val(),
                            icon: 'success',
                            target: document.getElementById('modal_informe_medico')
                        });
                    }
                }
            });
        }

        function MostrarHistorialPaciente(documento_paciente) {
            TablaHistorialClinico = $('#lista_historial_clinico_paciente').DataTable({
                bDestroy: true,
                language: SpanishDataTable(),
                responsive: true,
                ajax: {
                    url: RUTA + "ver-historial-del-paciente/" + documento_paciente + "?token_=" + TOKEN,
                    method: "GET",
                    dataSrc: "historial",
                },
                columns: [{
                        "data": "id_cita_medica"
                    },
                    {
                        "data": "paciente_"
                    },
                    {
                        "data": "fecha_cita",
                        render: function(fecha) { /// 2023-10-12

                            let Anio = fecha.substr(0, 4);
                            let Mes = fecha.substr(5, 2);
                            let Dia = fecha.substr(8, 2);
                            let fecha_ = Dia + "/" + Mes + "/" + Anio;
                            return fecha_;
                        }
                    },
                    {
                        "data": "nombre_esp",
                        render: function(esp) {
                            return "<b class='badge bg-info'>" + esp + "</b>";
                        }
                    },
                    {
                        "data": "id_cita_medica",
                        render: function(data) {


                            return "<a href='paciente/historial?v=" + data +
                                "' target='_blank' class='btn btn-danger btn-sm' id='historial_pac'><i class='bx bxs-receipt'></i></a>";
                        }
                    }
                ]
            });
        }

        /// MOSTRAMOS LOS PACIENTES DEL MÉDICO

        function ShowPacientes_() {
            PacientesDelMedico = $('#lista_pacientes_medico_').DataTable({
                responsive: true,
                retrieve: true,
                processing: true,
                language: SpanishDataTable(),
                ajax: {
                    url: RUTA + "ver-pacientes_del_medico?token_=" + TOKEN,
                    method: "GET",
                    dataSrc: "personas",
                },
                columns: [{
                        "data": "documento"
                    },
                    {
                        "data": "paciente_"
                    },
                    {
                        "data": "paciente_",
                        render: function() {
                            return "<button class='btn btn-info btn-sm' id='select_paciente'> <i class='bx bx-right-arrow-alt'></i></button>"
                        }
                    }
                ]
            }).ajax.reload();
        }

        /// seleccionar al paciente y ver su historial clinica
        function SelectPaciente(Tabla, Tbody) {
            $(Tbody).on('click', '#select_paciente', function() {
                let fila = $(this).parents('tr');

                if (fila.hasClass('child')) {
                    fila = fila.prev();
                }

                let Datos = Tabla.row(fila).data();

                let Paciente = Datos.paciente_;
                let DocumentoPaciente = Datos.documento;

                MostrarHistorialPaciente(DocumentoPaciente);
                $('#doc_paciente').attr('disabled', true);
                $('#doc_paciente').val(DocumentoPaciente + " - " + Paciente);
                $('#modal-pacientes_medico').modal('hide');
            });
        }
 
     /// editar los datos de la órden de laboratorio
     function editarOrdenLaboratorio(id)
     {
        $.ajax({
            url:RUTA+"orden/laboratorio/editar/"+id,
            method:"GET",
            data:{
                token_:TOKEN
            },
            success:function(response){
                response = JSON.parse(response);
               

                if(response.response != 'token-invalidate' && response.response !== 'no-authorized')
                {

                    $('#orden_lab_update').val(response.response.desc_analisis_requerida);
                    
                    $('#modal_evaluacion_pre_operatoria').modal("show");
                    $('#orden_lab_update').focus();
                }else{
                    Swal.fire({
                        title:"Mensaje del sistema!",
                        text:"Error al editar la órden de laboratorio,posiblemente por un token de seguridad o quizas no está authorizado para realizar esta acción!",
                        icon:"error"
                    })
                }
            }
        })
     }  
     
     /// modificar los datos de la órden de laboratorio
     function updateOrdenLaboratorio_(id,detalle_analisis_data)
     {
        $.ajax({
            url:RUTA+"orden/laboratorio/update/"+id,
            method:"POST",
            data:{
                token_:TOKEN,
                detalle_analisis:detalle_analisis_data
            },
            success:function(response)
            {
                response = JSON.parse(response);

                if(response.response === 'ok')
                {
                    Swal.fire({
                        title:"Mensaje del sistema!",
                        text:"Sus cambios han sido guardados correctamente! 😁",
                        icon:"success",
                        target:document.getElementById('modal_evaluacion_pre_operatoria')
                    });
                }else{
                    Swal.fire({
                        title:"Mensaje del sistema!",
                        text:"Error al guardar los cambios requeridos! 😢😔",
                        icon:"error",
                        target:document.getElementById('modal_evaluacion_pre_operatoria')
                    })
                }
            }
        });
     }
    </script>
@endsection
