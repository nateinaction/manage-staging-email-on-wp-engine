<?php

namespace ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class ReplacePHPMailer
{
    /**
     * @var CustomPHPMailer
     */
    private $customMailer;

    /**
     * Constructor
     *
     * @param CustomPHPMailer $customMailer
     */
    public function __construct(CustomPHPMailer $customMailer)
    {
        $this->customMailer = $customMailer;
    }

    /**
     * Hooks and returns global $phpmailer.
     *
     * @return PHPMailer $phpmailer
     *
     */
    public function doReplace()
    {
        global $phpmailer;
        return $this->replacePhpMailer($phpmailer);
    }

    /**
     * Replace the global PHPMailer with a CustomPHPMailer.
     *
     * @param PHPMailer $phpMailer WordPress' global PHPMailer
     * @return PHPMailer
     */
    public function replacePhpMailer(&$phpMailer = null)
    {
        $phpMailer = $this->customMailer;
        return $phpMailer;
    }
}
