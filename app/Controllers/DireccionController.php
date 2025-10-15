<?php

namespace App\Controllers;

use App\Models\ComprasModel;
use App\Models\DireccionEnvioModel;
use CodeIgniter\Controller;

class DireccionController extends Controller
{
    protected $comprasModel;
    protected $direccionModel;

    public function __construct()
    {
        $this->comprasModel = new ComprasModel();
        $this->direccionModel = new DireccionEnvioModel();
        helper(['form', 'url']);
    }

    // Muestra el formulario para una compra específica
    public function create($compraId)
    {
        // Validar que la compra exista
        $compra = $this->comprasModel->find($compraId);
        if (!$compra) {
            return redirect()->to('/')->with('error', 'Compra no encontrada.');
        }

        // Si ya tiene dirección, redirigir a confirmación
        $existente = $this->direccionModel->where('compra_id', $compraId)->first();
        if ($existente) {
            return redirect()->to('/direccion/confirmacion/' . $compraId);
        }

        return view('direccion_form', [
            'compra' => $compra,
            'validation' => \Config\Services::validation(),
        ]);
    }

    // Guarda la dirección enviada por POST
    public function store()
    {
        $data = $this->request->getPost();

        $rules = [
            'compra_id'     => 'required|is_natural_no_zero',
            'calle'         => 'required|min_length[3]',
            'ciudad'        => 'required|min_length[2]',
            'provincia'     => 'required|min_length[2]',
            'codigo_postal' => 'required|min_length[3]',
            'pais'          => 'required|min_length[2]',
            'telefono'      => 'permit_empty|min_length[6]',
            'nombre'        => 'permit_empty|max_length[100]',
            'apellido'      => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            $compra = $this->comprasModel->find($data['compra_id'] ?? 0);
            return view('direccion_form', [
                'compra' => $compra,
                'validation' => $this->validator,
            ]);
        }

        // Confirmar compra válida
        $compra = $this->comprasModel->find($data['compra_id']);
        if (!$compra) {
            return redirect()->to('/')->with('error', 'Compra no válida.');
        }

        // Evitar duplicados por compra
        $existe = $this->direccionModel->where('compra_id', $data['compra_id'])->first();
        if ($existe) {
            return redirect()->to('/direccion/confirmacion/' . $data['compra_id']);
        }

        $save = [
            'compra_id'     => (int) $data['compra_id'],
            'nombre'        => $data['nombre'] ?? null,
            'apellido'      => $data['apellido'] ?? null,
            'telefono'      => $data['telefono'] ?? null,
            'calle'         => $data['calle'],
            'numero'        => $data['numero'] ?? null,
            'piso'          => $data['piso'] ?? null,
            'depto'         => $data['depto'] ?? null,
            'ciudad'        => $data['ciudad'],
            'provincia'     => $data['provincia'],
            'codigo_postal' => $data['codigo_postal'],
            'pais'          => $data['pais'],
            'referencias'   => $data['referencias'] ?? null,
        ];

        $this->direccionModel->insert($save);

        return redirect()->to('/direccion/confirmacion/' . $data['compra_id'])->with('success', 'Dirección guardada correctamente');
    }

    // Vista de confirmación simple
    public function confirmacion($compraId)
    {
        $compra = $this->comprasModel->find($compraId);
        $direccion = $this->direccionModel->where('compra_id', $compraId)->first();

        if (!$compra || !$direccion) {
            return redirect()->to('/')->with('error', 'Datos no encontrados.');
        }

        return view('direccion_confirmacion', [
            'compra' => $compra,
            'direccion' => $direccion,
        ]);
    }
}
