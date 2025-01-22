@extends($this->Layouts("dashboard"))

@section("title_dashboard","crear tipo documento")

@section('css')
    <style>
        #tabla-tipodocumentos>thead>tr>th{
            background-color: #E6E6FA;
            color: #000;
            padding: 20px;
        }
    </style>
@endsection
@section('contenido')
<div class="mx-3">
    <div class="row">
        <div class="card mb-4">
            
            <div class="card-body">
                <div class="card-text">
                    <h5 class="float float-start mt-1 d-xl-block d-lg-block d-md-block d-sm-none d-none">Tipo documentos existentes</h5>
    
                    <button class="button-new float-end"
                        onclick="location.href='{{$this->route('new-tipo-documento')}}'"><i class='bx bxs-alarm-add'></i>
                        Agregar uno nuevo</button>
                </div>
                <br>
                @if ($this->ExistSession("mensaje"))
                <div class="m-2">

                    @if ($this->getSession("mensaje") == 1)
                    <div class="alert alert-success">
                        <b> Tipo de documento creado correctamente</b>
                    </div>
                    @endif
                    {{--- DESTRUIMOS LA SESSION----}}
                    @php
                    $this->destroySession("mensaje")
                    @endphp

                </div>
                @endif
              <div class="table-responsive">
                <table class="table table-bordered table-striped responsive nowrap" id="tabla-tipodocumentos" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>TIPO DOCUMENTO</th>
                            <th>GESTIONAR</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                        $Contador = 0;
                        @endphp
                        @foreach ($TipoDocumentos as $tipoDocumento)
                        @php $Contador++ @endphp
                        <tr>
                            <td>{{$Contador}}</td>
                            <td>{{strtoupper($tipoDocumento->name_tipo_doc)}}</td>
                            <td>
                                <div class="row">
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-3 col-12 m-xl-0 m-lg-0 m-md-0 m-1">
                                        <button class="btn rounded-pill btn-warning btn-sm" id="editar"
                                            onclick="editar(`{{$tipoDocumento->id_tipo_doc}}`,`{{$tipoDocumento->name_tipo_doc}}`)"><i
                                                class='bx bxs-edit-alt'></i></button>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-3 col-12 m-xl-0 m-lg-0 m-md-0 m-1">
                                        <form action="" method="post" id="form-delete">
                                            <button class="btn rounded-pill btn-danger btn-sm"
                                                onclick="event.preventDefault(); ConfirmDelete(`{{$tipoDocumento->id_tipo_doc}}`,`{{$tipoDocumento->name_tipo_doc}}`,event)"><i
                                                    class='bx bx-message-square-x'></i></button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
</div>

{{--- MODAL PARA EDITAR LOS TIPOS DE DOCUMENTOS -----}}
<div class="modal fade" id="modal-editar-tipo-documento" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFD700">
                <strong><h5>Editar tipo documento</h5></strong>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" method="post" id="form-modificar-tipo-documento">

                <div class="modal-body">
                    <input type="hidden" name="token_" value="{{$this->Csrf_Token()}}">
                    <div class="form-group">
                        <label for="documento" class="form-label">Tipo documento (*)</label>
                        <input type="text" name="documento" id="documento" class="form-control"
                            placeholder="Tipo documento...">
                        <div class="row justify-content-center">
                            <div class="col-xl-5 col-lg-5 col-md-6 col-sm-5 col-12 mt-2">
                                <button type="submit" class="btn rounded-pill btn-success form-control" id="save"
                                    name="save">Guardar <i class='bx bx-save'></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
{{-- JS ADICIONALES ---}}
<script src="{{URL_BASE}}public/js/control.js"></script>
<script>
    var IdTipoDoc;
    var URL_BASE_ = "{{URL_BASE}}";

 $(document).ready(function(){

    let BotonSave = $('#save');

    let InputTipoDocumento = $('');
  
    var TableTipoDocumentos =  $('#tabla-tipodocumentos').DataTable();

    /// boton editar

    BotonSave.click(function(evento){
     evento.preventDefault()
     GuardarCambios();

    });
    

 });  

 /// llama al modal para editar tipo documentos
 function editar(id,TipoDoc)
 {

    IdTipoDoc = id; /// capturamos el id

    $('#documento').val(TipoDoc)

    FocusInputModal('modal-editar-tipo-documento','documento');

    $('#modal-editar-tipo-documento').modal('show')
 }

 /// guardar cambios del tipo de documento

 function GuardarCambios()
 {
   DataForm = $('#form-modificar-tipo-documento').serialize();

   $.ajax({
    url:"{{$this->route('update-tipo-documento/')}}"+IdTipoDoc,
    method:"POST",
    data:DataForm,
    success:function(response)
    {
       if(response === 'existe')
       {
        Message("warning","¡ADVERTENCIA!","No se actualzaron los datos",'modal-editar-tipo-documento')
       }
       else
       {
        if(response == 1)
        {
            Message("success","¡SUCCESSFULL!","Datos modificados correctamente",'modal-editar-tipo-documento')  
        }
        else
        {
            Message("error","¡ERROR!","Error al actualizar los datos de tipo documento",'modal-editar-tipo-documento')
        }
       }
    }
   });

 
 }

  /// Eliminar tipo documento

  function ConfirmDelete(idEliminar,documento,evento)
 {

    evento.preventDefault()
    
    /// capturamos el id que deseamoa eliminar

    IdTipoDoc = idEliminar;

    /// token
 
   Swal.fire({
     title: 'Estas seguro de eliminar al tipo documento '+documento+'?',
     text: "al presionar que si, se inactivará este documento, y ya no será mostrado en el sistema para su uso correspondiente!",
     icon: 'question',
     showCancelButton: true,
     confirmButtonColor: '#3085d6',
     cancelButtonColor: '#d33',
     confirmButtonText: 'Si,eliminar!',
     target:document.getElementById('layout-menu')
   }).then((result) => {
  if (result.isConfirmed) {
     DeleteDocumento(IdTipoDoc);
   }
 })
  
 }

 function DeleteDocumento(id)
 {
    DataFormDel = $('#form-delete').serialize();
  $.ajax({
    url:"{{$this->route('destroy-tipo-documento/')}}"+id,
    method:"POST",
    data:{token_:"{{$this->Csrf_Token()}}"},
    success:function(response)
    {
    Message('success','Mensaje del sistema','Tipo documento eliminado',null);
    }
  })
 }
</script>
@endsection