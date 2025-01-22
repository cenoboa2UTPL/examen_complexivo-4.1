<?php

/**
 * Verificamos si existe sesión, caso contrario
 * lo creamos
 */

use Http\controllers\CajaController;

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

$route->get("/apertura/caja",[CajaController::class,'index']);

/// confirmar el cierre de caja por completo
$route->post("/confirma/cierre/caja/por/completo/{id}",[CajaController::class,'cerrarConfirmCaja']);

$route->get("/historial/caja",[CajaController::class,'ReporteCajaPorFechas']);

$route->post("/caja/{id}/delete",[CajaController::class,'EliminarCajaApertura']);