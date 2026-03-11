<?php
require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController
{
    public function index(): void
    {
        require_once __DIR__ . '/../../core/roles.php';
        require_modulo('analitica');

        $m = $this->model('DashboardModel');

        $ingresos_mes = $m->ingresosMes();
        $ingresos_anterior = $m->ingresosMesAnterior();
        $variacion = $ingresos_anterior > 0 ? round((($ingresos_mes - $ingresos_anterior) / $ingresos_anterior) * 100, 1) : 0;
        $total_clientes = $m->totalClientes();
        $nuevos_mes = $m->nuevosMes();
        $activas = $m->membresiasActivas();
        $por_vencer = $m->porVencer();
        $pagos_hoy = $m->pagosHoy();
        $ingresos_hoy = $m->ingresosHoy();
        $tasa_retencion = $m->tasaRetencion();

        $ingresos_meses = $m->ingresosMensuales();
        $ingresos_diarios = $m->ingresosDiarios();
        $planes_data = $m->planesVendidos();
        $metodos_data = $m->metodosPago();
        $dias_semana = $m->pagosDiaSemana();
        $segmentos = $m->segmentos();
        $riesgo = $m->clientesRiesgo();
        $ultimos = $m->ultimosPagos();
        $nuevos_por_mes = $m->nuevosPorMes();

        $this->render('dashboard/index', [
            'page' => 'analitica',
            'pageTitle' => 'Dashboard',
            'ingresos_mes' => $ingresos_mes,
            'ingresos_anterior' => $ingresos_anterior,
            'variacion' => $variacion,
            'total_clientes' => $total_clientes,
            'nuevos_mes' => $nuevos_mes,
            'activas' => $activas,
            'por_vencer' => $por_vencer,
            'pagos_hoy' => $pagos_hoy,
            'ingresos_hoy' => $ingresos_hoy,
            'tasa_retencion' => $tasa_retencion,
            'js_ingresos_meses' => json_encode($ingresos_meses),
            'js_ingresos_diarios' => json_encode($ingresos_diarios),
            'js_planes' => json_encode($planes_data),
            'js_metodos' => json_encode($metodos_data),
            'js_dias_semana' => json_encode($dias_semana),
            'js_segmentos' => json_encode($segmentos),
            'js_nuevos_mes' => json_encode($nuevos_por_mes),
            'riesgo' => $riesgo,
            'ultimos' => $ultimos,
        ]);
    }
}
