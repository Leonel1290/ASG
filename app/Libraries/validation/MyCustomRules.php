<?php

namespace App\Libraries\Validation;

use CodeIgniter\Validation\Rules; // Sigue siendo buena práctica extender de Rules

class MyCustomRules extends Rules
{
    /**
     * Valida que la cadena (contraseña) tenga al menos una longitud mínima.
     *
     * @param string $str La cadena a validar (la contraseña).
     * @param string|null $min_length_param El parámetro de longitud mínima (ej. '8').
     * @return bool
     */
    public function min_length_custom(string $str, ?string $min_length_param = null): bool
    {
        // Si no se proporciona un parámetro, asumimos un mínimo por defecto (ej. 8)
        $minLength = (int) ($min_length_param ?? 120);

        return strlen($str) >= $minLength;
    }

    /**
     * Puedes dejar la regla strong_password si quieres, pero si solo necesitas longitud,
     * la regla min_length_custom es más adecuada.
     * Si no vas a usar strong_password, puedes eliminarla.
     */
    /*
    public function strong_password(string $str): bool
    {
        // Aquí iría la lógica anterior con todos los requisitos
        // Pero para solo longitud, usaremos min_length_custom
        return true; // O podrías llamar a min_length_custom internamente
    }
    */
}