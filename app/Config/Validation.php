<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;
use App\Libraries\Validation\MyCustomRules; // <--- ¡Esta línea es clave!

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        MyCustomRules::class, // <--- ¡Añade esta línea para incluir tus reglas personalizadas!
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
    // Aquí puedes definir grupos de reglas personalizados si los necesitas,
    // por ejemplo:
    // public array $registration = [
    //     'username' => 'required|min_length[3]',
    //     'email'    => 'required|valid_email|is_unique[users.email]',
    // ];
    //
    // Y sus mensajes de error asociados:
    // public array $registration_errors = [
    //     'username' => [
    //         'min_length' => 'El nombre de usuario debe tener al menos 3 caracteres.',
    //     ],
    // ];
}