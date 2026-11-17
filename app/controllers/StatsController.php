<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/StudySession.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Subject.php';

class StatsController extends Controller
{
    public function index(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $modeloSessao = new StudySession();
        $modeloTarefa = new Task();
        $modeloMateria = new Subject();

        // ---------------- Horas estudadas ----------------
        $minutosHoje = $modeloSessao->minutesToday($idUsuario);
        $minutosSemana  = $modeloSessao->minutesThisWeek($idUsuario);
        $minutosMes = $modeloSessao->minutesThisMonth($idUsuario);
        $minutosTotal = $modeloSessao->minutesTotal($idUsuario);

        // ---------------- Tarefas ----------------
        $tarefasCriadas   = $modeloTarefa->countAll($idUsuario);
        $tarefasConcluidas = $modeloTarefa->countByStatus($idUsuario, 'concluida');
        $tarefasPendentes   = $modeloTarefa->countByStatus($idUsuario, 'pendente') + $modeloTarefa->countByStatus($idUsuario, 'em_andamento');
        $taxaConclusao = $modeloTarefa->completionRate($idUsuario);

        // ---------------- Dados para gráficos ----------------
        $minutosDiarios    = $modeloSessao->minutesByDay($idUsuario, 14);
        $tempoPorMateria    = $modeloMateria->progressByUser($idUsuario);
        $concluidasPorDia  = $modeloTarefa->completedByDay($idUsuario, 14);
        $evolucaoSemanal = $modeloSessao->minutesByWeek($idUsuario, 8);

        $this->render('estatisticas/index', [
            'minutosHoje'     => $minutosHoje,
            'minutosSemana'      => $minutosSemana,
            'minutosMes'     => $minutosMes,
            'minutosTotal'     => $minutosTotal,
            'tarefasCriadas'     => $tarefasCriadas,
            'tarefasConcluidas'   => $tarefasConcluidas,
            'tarefasPendentes'     => $tarefasPendentes,
            'taxaConclusao'   => $taxaConclusao,
            'minutosDiarios'     => $minutosDiarios,
            'tempoPorMateria'     => $tempoPorMateria,
            'concluidasPorDia'   => $concluidasPorDia,
            'evolucaoSemanal'  => $evolucaoSemanal,
        ], 'app');
    }
}
