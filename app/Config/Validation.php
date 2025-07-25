// app/Config/Validation.php

<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    /**
     * Stores the classes that contain the
     * rules that are available.
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        // La línea \App\Libraries\Validation\MyCustomRules::class, DEBE SER ELIMINADA.
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // ... El resto del archivo permanece igual ...
}