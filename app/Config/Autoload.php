// app/Config/Autoload.php

<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

/**
 * @immutable
 */
class Autoload extends AutoloadConfig
{
    /**
     * -------------------------------------------------------------------
     * Namespaces
     * -------------------------------------------------------------------
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
        // Originalmente, solo tenías la línea de arriba.
        // Si tenías 'Config' => APPPATH . 'Config', déjala. Si no, quítala.
        // La línea 'App\Libraries\Validation' debe ser eliminada.
    ];

    /**
     * -------------------------------------------------------------------
     * Class Map
     * -------------------------------------------------------------------
     */
    public $classmap = [];

    /**
     * -------------------------------------------------------------------
     * Files
     * -------------------------------------------------------------------
     */
    public $files = [];

    /**
     * -------------------------------------------------------------------
     * Helpers
     * -------------------------------------------------------------------
     */
    public $helpers = [];
}