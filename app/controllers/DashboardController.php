<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/StudySession.php';
require_once __DIR__ . '/../models/Goal.php';
require_once __DIR__ . '/../models/Assessment.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $modeloMateria  = new Subject();
        $modeloTarefa     = new Task();
        $modeloSessao  = new StudySession();
        $modeloMeta     = new Goal();
        $modeloMeta->syncAutoProgress($idUsuario);
        $modeloAvaliacao = new Assessment();

        $minutosHoje   = $modeloSessao->minutesToday($idUsuario);
        $totalMaterias  = count($modeloMateria->forUser($idUsuario));
        $tarefasPendentes   = $modeloTarefa->countByStatus($idUsuario, 'pendente') + $modeloTarefa->countByStatus($idUsuario, 'em_andamento');
        $totalTarefas     = $modeloTarefa->countAll($idUsuario);
        $percentualMetas   = $modeloMeta->completionPercentage($idUsuario);
        $metasConcluidas = count(array_filter($modeloMeta->forUser($idUsuario), fn($g) => (int) $g['is_completed'] === 1));
        $totalMetas     = count($modeloMeta->forUser($idUsuario));

        $progressoMaterias = $modeloMateria->progressByUser($idUsuario);
        $minutosSemanais     = $modeloSessao->minutesByDay($idUsuario, 7);
        $tarefasProximas     = $modeloTarefa->upcoming($idUsuario, 4);
        $avaliacoesProximas = $modeloAvaliacao->upcoming($idUsuario, 4);
        $sessoesRecentes    = $modeloSessao->recent($idUsuario, 4);
        $metas             = $modeloMeta->forUser($idUsuario, 4);

        $this->render('dashboard/index', [
            'nomeUsuario'            => $usuario['name'],
            'minutosHoje'        => $minutosHoje,
            'metaMinutosDiaria'    => 120,
            'totalMaterias'       => $totalMaterias,
            'tarefasPendentes'        => $tarefasPendentes,
            'totalTarefas'          => $totalTarefas,
            'percentualMetas'        => $percentualMetas,
            'metasConcluidas'      => $metasConcluidas,
            'totalMetas'          => $totalMetas,
            'progressoMaterias'    => $progressoMaterias,
            'minutosSemanais'       => $minutosSemanais,
            'tarefasProximas'       => $tarefasProximas,
            'avaliacoesProximas' => $avaliacoesProximas,
            'sessoesRecentes'      => $sessoesRecentes,
            'metas'               => $metas,
        ], 'app');
    }
}
