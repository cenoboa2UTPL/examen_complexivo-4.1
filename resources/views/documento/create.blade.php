@extends($this->Layouts("dashboard"))

@section("title_dashboard","crear tipo documento")

@section('contenido')
<div class="mx-3">
  <div class="row">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center justify-content-between">
        <h4 class="mb-0">crear documento</h4>
      </div>

      <form action="{{$this-> route('save-tipo-documento')}}" method="post">
        <input type="hidden" value="{{$this->Csrf_Token()}}" name="token_">
        <div class="card-body">
          @if ($this->ExistSession("mensaje"))
          <div class="m-2">

             @if ($this->getSession("mensaje")== 'error')
             <div class="alert alert-danger">
              <b>Complete el campo de nombre de tipo de documento</b>
              </div>
              @else 
              <div class="alert alert-warning">
                <b>Ya existe este tipo de documento</b>
               </div>
             
             @endif
              {{--- DESTRUIMOS LA SESSION----}}
              @php
              $this->destroySession("mensaje")
              @endphp

          </div>
          @endif
          <div class="row">
            <label for="" class="col-md-2 col-form-label"><b>Nombre(*)</b></label>
            <div class="col-md-10">
              <input type="text" class="form-control mb-3" name="name-tipo-doc" id="name-tipo-doc"
                placeholder="Tipo documento" value="{{$this->old("name-tipo-doc")}}" autofocus>
            </div>
          </div>
        </div>

        <div class="row justify-content-center mb-4 mx-3">
          <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-12 m-xl-0 mlg-0 m-md-0 m-1">
            <button class="btn_success_person" name="save" style="width: 100%"><b>Guardar <i class='bx bxs-save'></i></b></button>
          </div>

          <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5 col-12 m-xl-0 mlg-0 m-md-0 m-1">
            <button class="btn btn-danger rounded form-control" name="cancelar"><b>Cancelar </b><i
                class='bx bx-window-close'></i></button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection