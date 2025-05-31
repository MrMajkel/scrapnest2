<?php

require_once __DIR__.'/AppController.php';

class DefaultController extends AppController {

    public function index() {
        $this->render('logowanie');
    }

    public function formularze() {
        $this->render('formularze');
    }

    public function fv_sprzedaz() {
        $this->render('fv_sprzedaz');
    }

    public function fv_zakup() {
        $this->render('fv_zakup');
    }

    public function kontrahenci() {
        $this->render('kontrahenci');
    }

    public function logowanie() {
        $this->render('logowanie');
    }

    public function panel() {
        $this->render('panel');
    }

    public function raporty() {
        $this->render('raporty');
    }

    public function rejestracja() {
        $this->render('rejestracja');
    }

    public function reset_hasla() {
        $this->render('reset_hasla');
    }

    public function uzytkownicy() {
        $this->render('uzytkownicy');
    }
}
