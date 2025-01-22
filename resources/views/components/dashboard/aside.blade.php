 
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme @yield('clase_ocultar')">
  <div class="app-brand demo m-1 text-center">
    
    @if (!file_exists("public/asset/empresa/".$this->BusinesData()[0]->logo))
    <img src="{{$this->asset("img/lgo_clinica_default.jpg")}}" id="imagen_logo" style="width:180px;height:105px" >
    @else 
    <img src="{{$this->asset(isset($this->BusinesData()[0]->logo) ?"empresa/".$this->BusinesData()[0]->logo:"img/lgo_clinica_default.jpg")}}" id="imagen_logo" style="width:240px;height:91px" >
    @endif
   
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>
    
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
    {{--- Dashboard del sistema(inicial del sistema)---}}
      <li class="menu-item active">
        <a href="{{$this->route('dashboard')}}" class="menu-link text-white">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div data-i18n="Analytics">Dashboard 
          </div>
        </a>
      </li>
      
      <!-- Configuración del sistema -->
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <img src="{{$this->asset('img/icons/unicons/config.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Layouts" class="letra" style="color: #0f0606"><b>Configuración </b></div>
        </a>
      
        <ul class="menu-sub">
          <li class="menu-item">
            <a href="{{$this->route('profile/editar')}}" class="menu-link">
              <div data-i18n="Without navbar" class="letra" >Actualizar perfíl</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="{{$this->route('config/sistema')}}" class="menu-link">
              <div data-i18n="Without navbar" class="letra" >Sistema</div>
            </a>
          </li>
          @if ($this->authenticado())
              @if ($this->profile()->rol === 'Director' || $this->profile()->rol === 'Médico' ||
              $this->profile()->rol === 'admin_farmacia' || $this->profile()->rol === 'admin_general')
              <li class="menu-item">
                <a href="{{$this->route($this->profile()->rol === 'admin_farmacia' ? 'Configurar-datos-farmacia':'Configurar-datos-empresa-EsSalud')}}" class="menu-link">
                  
                  <div data-i18n="Container" class="letra">
                    @if ($this->profile()->rol === 'admin_farmacia')
                        Datos de la farmacia
                        @else 
                        Datos de la clínica
                    @endif
                  </div>
                </a>
              </li>
              @endif
          @endif
        </ul>
      </li>

      {{-- VER MI INFORME MÉDICO ---}}

      @if ($this->authenticado() and $this->profile()->rol === 'Paciente')
      <li class="menu-item">
        <a href="{{$this->route("paciente/consultar_informe_medico")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/informe_medico.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b style="color: #000000">Informe médico</b></div>
        </a>
      </li>
      @endif
     
      <!-- Tipo de documentos -->
      @if ($this->authenticado() and ($this->profile()->rol === 'Director' || $this->profile()->rol === 'admin_general' || $this->profile()->rol === 'admin_farmacia'))
      <li class="menu-item">
        <a href="{{$this->route("user_gestion")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/user.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Gestionar usuarios</b></div>
        </a>
      </li>
      @endif

      @if ($this->authenticado() and ($this->profile()->rol === 'Director'  || $this->profile()->rol === 'admin_general' || $this->profile()->rol === 'admin_farmacia'))
      <li class="menu-item">
        <a href="{{$this->route("tipo-documentos-existentes")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/documento.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Cédula de identidad</b></div>
        </a>
      </li>
      @endif

      @if ($this->authenticado() and ( $this->profile()->rol === 'admin_farmacia' || $this->profile()->rol === 'Farmacia' ) )
      <li class="menu-item">
        <a href="{{$this->route("egresos")}}" class="menu-link">
          <img src="{{ $this->asset('img/icons/unicons/egresos.ico') }}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Gestión de gastos</b></div>
        </a>
      </li>
      @endif
      @if ($this->authenticado() and ($this->profile()->rol === 'Director' || $this->profile()->rol === 'admin_general' || $this->profile()->rol === 'admin_farmacia'  || $this->profile()->rol === 'Farmacia') )
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <img src="{{$this->asset('img/icons/unicons/confirm_caja.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Layouts" class="letra text-dark" style="color: #0f0606"><b>Gestionar</b></div>
        </a>
      
        <ul class="menu-sub">
          @if ($this->authenticado() and ($this->profile()->rol === 'Director' || $this->profile()->rol === 'admin_general' || $this->profile()->rol === 'admin_farmacia'))
          <li class="menu-item">
            <a href="{{$this->route("departamentos")}}" class="menu-link">
              <div data-i18n="Analytics" class="letra text-dark"><b>Ciudades</b></div>
            </a>
          </li>
          @endif
          @if ($this->profile()->rol === 'Director' || $this->profile()->rol === 'admin_farmacia' || $this->profile()->rol === 'Farmacia')
          <li class="menu-item">
            <a href="{{$this->route("apertura/caja")}}" class="menu-link">
             
              <div data-i18n="Analytics" class="letra text-dark"><b style="color: #010101">Administrar caja</b></div>
            </a>
          </li>
          @endif
          @if ($this->profile()->rol === 'Director' || $this->profile()->rol === 'admin_general' || $this->profile()->rol === 'admin_farmacia')
          <li class="menu-item">
            <a href="{{$this->route("resumen/caja/validate-cierre-caja")}}" class="menu-link">
              <div data-i18n="Analytics" class="letra text-dark"><b style="color: #010101">Resumen de caja</b></div>
            </a>
          </li>
          @endif
        </ul>
      </li>
    
      @endif

      
      @if ($this->profile()->rol === 'Director' || $this->profile()->rol === 'admin_general' || $this->profile()->rol === 'admin_farmacia' || $this->profile()->rol === 'Farmacia')
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <img src="{{$this->asset('img/icons/unicons/aplicaciones.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Layouts" class="letra text-dark" style="color: #0f0606"><b>Aplicaciones </b></div>
        </a>
      
        <ul class="menu-sub">
          <!-- modificado ---->
          @if ($this->profile()->rol === 'Farmacia' || $this->profile()->rol === 'admin_general' || $this->profile()->rol === 'admin_farmacia')
          <li class="menu-item">
            <a href="{{$this->route("app/farmacia")}}" class="menu-link">
              <div data-i18n="Analytics" class="letra"> Farmacia </div>
            </a>
          </li>
          @endif 
        </ul>
      </li>
      @endif    

      @if ($this->authenticado() and ($this->profile()->rol === 'Director' or $this->profile()->rol === 'admin_general' or $this->profile()->rol === 'Admisión' || $this->profile()->rol === 'Médico'))
      <li class="menu-item">
        <a href="{{$this->route("paciente")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/paciente.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Pacientes</b></div>
        </a>
      </li>
      @endif

      @if ($this->authenticado() and ($this->profile()->rol === 'admin_general' or $this->profile()->rol === 'Director' || $this->profile()->rol === 'Médico'))
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <img src="{{$this->asset('img/icons/unicons/medico.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Layouts" class="letra text-dark"><b>Médicos</b></div>
        </a>
      
        <ul class="menu-sub">
          @if ($this->authenticado() and ($this->profile()->rol === 'Director' || $this->profile()->rol === 'admin_general'))
          <li class="menu-item">
            <a href="{{$this->route("medicos")}}" class="menu-link">

              {{-- SOLO ADMINISTARDORES--}}
              <div data-i18n="Without menu" class="letra" style="color: ##696969">Gestionar médico</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="{{$this->route("medico/servicios")}}" class="menu-link">

              {{-- SOLO ADMINISTARDORES--}}
              <div data-i18n="Without menu" class="letra" style="color: ##696969">Servicios</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="{{$this->route("medico/citas-realizados")}}" class="menu-link">

              {{-- SOLO ADMINISTARDORES--}}
              <div data-i18n="Without menu" class="letra" style="color: ##696969">citas realizados</div>
            </a>
          </li>
          @endif
          {{---SOLO PARA LOS MÉDICOS --}}
          @if ($this->profile()->rol === 'Médico')
          <li class="menu-item">
            <a href="{{str_replace(" ","_",$this->route($this->profile()->name).'/horarios')}}" class="menu-link">
              <div data-i18n="Without navbar" class="letra">mis horarios</div>
            </a>
          </li>

          <li class="menu-item">
            <a href="{{$this->route("medico/import-dias-de-atencion")}}" class="menu-link">
              <div data-i18n="Without navbar" class="letra">Dias de atención</div>
            </a>
          </li>
          @endif
          @if ($this->authenticado() and $this->profile()->rol === 'Médico')
          <li class="menu-item">
            <a href="{{$this->route("medico/mis_servicios")}}" class="menu-link">

              {{-- SOLO ADMINISTARDORES--}}
              <div data-i18n="Without menu" class="letra" style="color: ##696969">Mis servicios</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="{{$this->route("medico/especialidades")}}" class="menu-link">

              {{-- SOLO ADMINISTARDORES--}}
              <div data-i18n="Without menu" class="letra" style="color: ##696969">Especialidades</div>
            </a>
          </li>
          @endif
       
        </ul>
      </li>
      @endif
      @if ($this->profile()->rol === 'Paciente' and isset($this->profile()->id_persona))
      <li class="menu-item">
       <a href="{{$this->route('seleccionar-especialidad')}}" class="menu-link">
        <img src="{{$this->asset('img/icons/unicons/ctma.ico')}}" class="menu-icon" alt="">
         <div data-i18n="Without menu" class="letra text-dark"><b>Sacar cita</b></div>
       </a>
     </li>
      @endif
      @if ($this->profile()->rol === 'Paciente' and isset($this->profile()->id_persona))
      <li class="menu-item">
       <a href="{{$this->route('citas-realizados')}}" class="menu-link">
        <img src="{{$this->asset('img/icons/unicons/citas_save.ico')}}" class="menu-icon" alt="">
         <div data-i18n="Without menu" class="letra"><b style="color: #0b0606">Citas realizados</b></div>
       </a>
     </li>
     <li class="menu-item">
      <a href="{{$this->route('mis-testimonios-publicados')}}" class="menu-link">
       <img src="{{$this->asset('img/icons/unicons/comentarios.ico')}}" class="menu-icon" alt="">
        <div data-i18n="Without menu" class="letra text-dark"><b>Mis testimonios</b></div>
      </a>
    </li>
      @endif
      @if ($this->authenticado())
          @if ($this->profile()->rol === 'Admisión' || $this->profile()->rol === 'Director' || $this->profile()->rol === 'Médico' || $this->profile()->rol === 'admin_general' )
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <img src="{{$this->asset('img/icons/unicons/cita_.ico')}}" class="menu-icon" alt="">
              <div data-i18n="Layouts" class="letra text-dark"><b>Cita</b></div>
            </a>
          
            <ul class="menu-sub">
    
              @if ($this->authenticado())
    
               @if ($this->profile()->rol === 'Admisión' or $this->profile()->rol === 'Médico' || $this->profile()->rol === 'Director')
               <li class="menu-item">
                <a href="{{$this->route('crear-nueva-cita-medica')}}" class="menu-link">
    
                  <div data-i18n="Without menu" class="letra">nueva cita médica</div>
                </a>
              </li>
               @endif
                  
              @endif
    
              @if ($this->authenticado())
    
               @if ($this->profile()->rol === 'Admisión' || $this->profile()->rol === 'Médico' || $this->profile()->rol === 'Director' || $this->profile()->rol === 'admin_general')
               <li class="menu-item">
                <a href="{{$this->route("citas-programados")}}" class="menu-link">
                  <div data-i18n="Without navbar" class="letra">citas programados</div>
                </a>
              </li>
               @endif
                  
              @endif
           
            </ul>
          </li>
          @endif
      @endif
      
      @if ($this->profile()->rol === 'Enfermera-Triaje' or $this->profile()->rol === 'Médico')
      <li class="menu-item">
        <a href="{{$this->route("triaje/pacientes")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/triaje.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Pacientes (Triaje)</b></div>
        </a>
      </li>
      @endif
      
      @if ($this->profile()->rol === 'Médico')
      <li class="menu-item">
        <a href="{{$this->route("nueva_atencion_medica")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/atencion_medica.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Atención médica</b></div>
        </a>
      </li>
      <li class="menu-item">
        <a href="{{$this->route("paciente/evaluacion_informes")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/informes_.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Evaluación e informes</b></div>
        </a>
      </li>
      <li class="menu-item">
        <a href="{{$this->route("medico/generate/recibo/paciente")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/pago.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Generar recibo</b></div>
        </a>
      </li>
      <li class="menu-item">
        <a href="{{$this->route("generate-receta-medica")}}" class="menu-link">
          <img src="{{$this->asset('img/icons/unicons/receta.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Generar receta</b></div>
        </a>
      </li>
      @endif 

      @if ($this->profile()->rol === 'Director' or $this->profile()->rol === 'admin_general' || $this->profile()->rol === 'admin_farmacia' || $this->profile()->rol === 'Farmacia' )
      <li class="menu-item">
        <a href="{{$this->route("reportes")}}" class="menu-link menu-toggle">
          <img src="{{$this->asset('img/icons/unicons/reporte.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Analytics" class="letra text-dark"><b>Reportes</b></div>
        </a>

        <ul class="menu-sub">
          <li class="menu-item">
            <a href="{{$this->route("reportes")}}" class="menu-link">

              {{-- SOLO ADMINISTARDORES--}}
              <div data-i18n="Without menu" class="letra" style="color: ##696969">Reporte general</div>
            </a>
          </li>
        @if ($this->profile()->rol === 'Director' or $this->profile()->rol === 'admin_general')
          <li class="menu-item">
            <a href="{{$this->route("medico/ingresos-por-mes-detallado")}}" class="menu-link">

              {{-- SOLO ADMINISTARDORES--}}
              <div data-i18n="Without menu" class="letra" style="color: ##696969">Ingresos mensual</div>
            </a>
          </li>
          @endif
        </ul>
      </li>
      @endif
      @if ($this->profile()->rol === 'Admisión' || $this->profile()->rol === 'Médico')
      <li class="menu-item">
        <a href="{{$this->route('clinica/notificaciones')}}" class="menu-link">
         <img src="{{$this->asset('img/icons/unicons/alarma.ico')}}" class="menu-icon" alt="">
          <div data-i18n="Without menu" class="letra text-dark"><b>Notificaciones <span class="badge bg-danger">
          {{isset($this->CantidadNotificaciones()->cantidad_notificaciones) ? $this->CantidadNotificaciones()->cantidad_notificaciones:0}}  
          </span></b></div>
        </a>
      </li>
      @endif
    </ul>
  </aside>