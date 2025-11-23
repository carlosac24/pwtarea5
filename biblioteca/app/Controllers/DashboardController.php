<?php

class DashboardController extends Controller
{
    public function index()
    {
        Auth::requireLogin();
        $statsModel = new Stats();
        $stats = $statsModel->getCounts();
        $this->view('dashboard', ['stats' => $stats]);
    }
}
