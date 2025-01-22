@extends($this->Layouts("dashboard"))

@section("title_dashboard","Especialidades")

@section('css')
<style>
    .card{
     cursor: pointer;
    }
</style>
@endsection
 
@section("contenido")
<div class="row">
  @if (isset($especialidades))
      @foreach ($especialidades as $esp)
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
           <a href="seleccionar-medico?esp_id={{$esp->id_especialidad}}&&especialidad={{$esp->nombre_esp}}">
            <div class="card" style="background: #00BFFF">
                <div class="card-body">
                  <div class="card-text"><h5 class="text-white">Seleccionar <i class='bx bxs-hand-up'></i></h5></div>
                  <div class="card-text">
                    <b style="color: rgb(50, 34, 190)">{{$esp->nombre_esp}}</b>
                  </div>
                </div>
               </div>
           </a>
        </div>
      @endforeach
      @else
      <div class="col">
        <div class="alert alert-danger">
            No hay especialidades para mostrar... 😥
          </div>
      </div>
  @endif
</div>
@endsection