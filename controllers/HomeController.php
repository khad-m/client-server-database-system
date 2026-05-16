<?php
class HomeController {
    public function index() {
        // Load the view for the home page
        require_once 'views/home/index.phtml';
    }
}
