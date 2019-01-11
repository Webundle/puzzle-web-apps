<?php

namespace Puzzle\NewsletterBundle;

final class MailCommands {
    
    /**
     * Command for sending scheduled mails
     */
    const SEND_MAIL = 'puzzle:newsletter:schedule-mail-sending';
}