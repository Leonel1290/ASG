<?php

namespace App\Libraries\Validation;

class MyCustomRules
{
    /**
     * Valida que la cadena contenga al menos un número, una mayúscula, una minúscula y un símbolo.
     * Permite un conjunto específico de símbolos comunes.
     *
     * @param string $str La cadena a validar (la contraseña).
     * @return bool
     */
    public function strong_password(string $str): bool
    {
        // La contraseña debe tener al menos 8 caracteres (puedes ajustar esto)
        if (strlen($str) < 8) {
            return false;
        }

        // Al menos una letra minúscula
        if (!preg_match('/[a-z]/', $str)) {
            return false;
        }

        // Al menos una letra mayúscula
        if (!preg_match('/[A-Z]/', $str)) {
            return false;
        }

        // Al menos un número
        if (!preg_match('/[0-9]/', $str)) {
            return false;
        }

        // Al menos un símbolo de un conjunto común
        // Aquí puedes definir los símbolos específicos que quieres permitir.
        // Ejemplo: !@#$%^&*()-_+=[]{}|;:,.<>?
        // Este regex permite los símbolos más comunes. Si necesitas otros, añádelos.
        if (!preg_match('/[!@#$%^&*()\-_+=\[\]{}|;:,.<>?\/\'"`~]/', $str)) {
            return false;
        }

        return true;
    }
}