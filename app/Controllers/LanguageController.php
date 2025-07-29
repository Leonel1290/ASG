<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class LanguageController extends Controller
{
    public function changeLanguage()
    {
        $idioma = $this->request->getPost('idioma');
        
        // Validar idiomas permitidos
        $idiomasPermitidos = ['es', 'en'];
        if (in_array($idioma, $idiomasPermitidos)) {
            session()->set('lang', $idioma);
            
            // Opcional: Guardar preferencia en DB si el usuario está logueado
            if (session()->has('id')) {
                $userModel = new \App\Models\UserModel();
                $userModel->update(session()->get('id'), ['idioma' => $idioma]);
            }
        }
        
        return redirect()->back()->with('success', 'Idioma actualizado');
    }
}