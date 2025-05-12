<?php

namespace App\Enums;

enum MessageLogTarget: string
{
    case WHATSAPP = 'whatsapp';
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';
    case DATABASE = 'database';
}
