<?php
/**
 * app/controllers/HomeController.php
 *
 * Landing page pública do StudyHub.
 */

require_once __DIR__ . '/../core/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        $this->render('home/index', [], 'landing');
    }
}
