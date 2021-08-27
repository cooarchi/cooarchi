<?php

declare(strict_types=1);

use CooarchiApp\Command;

return [
    'laminas-cli' => [
        'commands' => [
            'cooArchi:setup' => Command\SetupCommand::class,
        ],
    ],
];
