<?php

namespace ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

interface CustomPHPMailer
{
    public function send();
}