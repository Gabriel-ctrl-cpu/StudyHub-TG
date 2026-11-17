<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/Assessment.php';

class SearchController extends Controller
{
    public function index(): void
    {
        $usuario = $this->requireAuth();
        $idUsuario = (int) $usuario['id'];

        $termo = trim($_GET['q'] ?? '');

        if (mb_strlen($termo) < 2) {
            $this->json(['term' => $termo, 'results' => []]);
        }

        $resultados = [];

        // Matérias
        foreach ($this->searchSubjects($idUsuario, $termo) as $s) {
            $resultados[] = [
                'group' => 'Matérias',
                'icon'  => 'fa-book',
                'title' => $s['name'],
                'subtitle' => $s['description'] ?: null,
                'color' => $s['color'],
                'url'   => APP_URL . '/materias/' . $s['id'] . '/editar',
            ];
        }

        // Tarefas
        foreach ($this->searchTasks($idUsuario, $termo) as $t) {
            $resultados[] = [
                'group' => 'Tarefas',
                'icon'  => 'fa-list-check',
                'title' => $t['title'],
                'subtitle' => $t['subject_name'] ?: null,
                'color' => $t['subject_color'] ?? null,
                'url'   => APP_URL . '/tarefas/' . $t['id'] . '/editar',
            ];
        }

        // Anotações
        foreach ((new Note())->search($idUsuario, $termo) as $n) {
            $resultados[] = [
                'group' => 'Anotações',
                'icon'  => 'fa-note-sticky',
                'title' => $n['title'],
                'subtitle' => $n['subject_name'] ?: null,
                'color' => $n['subject_color'] ?? null,
                'url'   => APP_URL . '/anotacoes/' . $n['id'] . '/editar',
            ];
        }

        // Avaliações
        foreach ($this->searchAssessments($idUsuario, $termo) as $a) {
            $resultados[] = [
                'group' => 'Avaliações',
                'icon'  => 'fa-file-pen',
                'title' => $a['title'],
                'subtitle' => $a['subject_name'] ?: null,
                'color' => $a['subject_color'] ?? null,
                'url'   => APP_URL . '/avaliacoes/' . $a['id'] . '/editar',
            ];
        }

        $this->json(['term' => $termo, 'results' => array_slice($resultados, 0, 20)]);
    }

    private function searchSubjects(int $idUsuario, string $termo): array
    {
        $modeloMateria = new Subject();
        $linhas = $modeloMateria->forUser($idUsuario, true);
        $termo = mb_strtolower($termo);
        return array_values(array_filter($linhas, fn($s) => str_contains(mb_strtolower($s['name']), $termo)));
    }

    private function searchTasks(int $idUsuario, string $termo): array
    {
        return (new Task())->filtered($idUsuario, ['search' => $termo]);
    }

    private function searchAssessments(int $idUsuario, string $termo): array
    {
        $linhas = (new Assessment())->forUser($idUsuario);
        $termo = mb_strtolower($termo);
        return array_values(array_filter($linhas, fn($a) => str_contains(mb_strtolower($a['title']), $termo)));
    }
}
