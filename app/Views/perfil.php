<?= $this->extend('perfil/perfil_layout') ?>

<?= $this->section('content') ?>
<div class="container my-5">

    <?php if (session('success')): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> <?= session('success') ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?></div>
    <?php endif; ?>
    <?php if (session('info')): ?>
        <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i> <?= session('info') ?></div>
    <?php endif; ?>


    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-user-circle me-2"></i> Mi Perfil</h5>
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> <?= esc(session()->get('nombre')) ?></p>
            <p><strong>Email:</strong> <?= esc($userEmail) ?></p>
        </div>
    </div>

    <div class="devices-section-title">
        <h2 style="margin: 0;"><i class="fas fa-microchip me-2"></i> Mis Dispositivos Enlazados</h2>
        <button id="show-add-mac-form" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-2"></i> Añadir Dispositivo
        </button>
    </div>


    <div id="add-mac-form" style="display: none;">
        <form action="<?= base_url('/enlace/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="mac">Dirección MAC:</label>
                <input type="text" class="form-control" id="mac" name="mac" placeholder="Ej: XX:XX:XX:XX:XX:XX" required>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-link me-2"></i> Enlazar Dispositivo</button>
        </form>
        <hr class="my-4" style="border-color: #4a5568;">
    </div>


    <?php if (empty($dispositivosEnlazados)): ?>
        <p>No tienes dispositivos enlazados aún.</p>
    <?php else: ?>
        <form id="delete-devices-form" action="<?= base_url('/perfil/eliminar-dispositivos') ?>" method="post">
            <?= csrf_field() ?>
            <button type="button" id="delete-selected-btn" class="btn btn-danger mb-4">
                <i class="fas fa-trash-alt me-2"></i> Eliminar Seleccionados
            </button>

            <div class="row">
                <?php foreach ($dispositivosEnlazados as $dispositivo): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="product-card">
                            <div class="discount-ribbon">ASG Sentinel</div>

                            <input type="checkbox" name="macs[]" value="<?= esc($dispositivo->MAC) ?>" class="delete-checkbox">

                            <div class="product-image-container">
                                <img src="<?= base_url('/imagenes/ASG_SENTINEL.jpg') ?>" class="product-image" alt="ASG Sentinel Device">
                            </div>
                            <div class="product-body">
                                <div class="product-name">
                                    <?= esc($dispositivo->nombre ?: 'Dispositivo sin nombre') ?>
                                </div>
                                
                                <div class="price-section">
                                    MAC: <?= esc(substr($dispositivo->MAC ?? 'Desconocida', -5)) ?>...
                                    <span class="old-price">MAC Completa</span>
                                </div>

                                <div class="product-details">
                                    Ubicación: <?= esc($dispositivo->ubicacion ?: 'Desconocida') ?>
                                </div>
                            </div>
                            
                            <div class="product-actions">
                                <a href="<?= base_url('/perfil/dispositivo/editar/' . esc($dispositivo->MAC)) ?>" 
                                   class="action-button edit-btn" 
                                   title="Editar Dispositivo">
                                    <i class="fas fa-edit me-2"></i> Editar
                                </a>
                                
                                <a href="<?= base_url('/detalles/' . esc($dispositivo->MAC)) ?>" 
                                   class="action-button details-btn" 
                                   title="Ver Detalles">
                                    <i class="fas fa-chart-bar me-2"></i> Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
    <?php endif; ?>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas desenlazar los dispositivos seleccionados?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Nueva sección para mostrar lecturas que superaron el umbral -->
    <div class="card mt-5">
        <div class="card-header">
            <h5 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i> Historial de Alertas de Gas</h5>
        </div>
        <div class="card-body">
            <?php if (empty($lecturasSuperaronUmbral)): ?>
                <p>No hay registros de alertas de gas.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Dispositivo</th>
                                <th>MAC</th>
                                <th>Nivel de Gas</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lecturasSuperaronUmbral as $lectura): ?>
                                <tr>
                                    <td><?= esc($lectura['nombre_dispositivo']) ?></td>
                                    <td><?= esc($lectura['MAC']) ?></td>
                                    <td><span class="badge bg-danger"><?= esc($lectura['nivel_gas']) ?></span></td>
                                    <td><?= esc($lectura['fecha']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Script para manejar la visibilidad del formulario de añadir MAC y el modal de eliminación
    document.addEventListener('DOMContentLoaded', function () {
        const deleteSelectedBtn = document.getElementById('delete-selected-btn');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        const deleteDevicesForm = document.getElementById('delete-devices-form');
        const confirmDeleteModalElement = document.getElementById('confirmDeleteModal');
        const confirmDeleteModal = new bootstrap.Modal(confirmDeleteModalElement); // Get Bootstrap modal instance
        const addMacForm = document.getElementById('add-mac-form');
        const showAddMacFormButton = document.getElementById('show-add-mac-form');

        // Mostrar/ocultar formulario de añadir MAC
        if (showAddMacFormButton && addMacForm) { // Check if both elements exist
            showAddMacFormButton.addEventListener('click', function () {
                if (addMacForm.style.display === "none" || addMacForm.style.display === "") {
                    addMacForm.style.display = "block"; // Show
                    this.innerHTML = '<i class="fas fa-minus-circle me-2"></i> Ocultar Formulario'; // Change text
                } else {
                    addMacForm.style.display = "none"; // Hide
                    this.innerHTML = '<i class="fas fa-plus-circle me-2"></i> Añadir Dispositivo'; // Change text
                }
            });
        }


        // Mostrar modal de confirmación al hacer clic en "Eliminar Seleccionados"
        if (deleteSelectedBtn && confirmDeleteModalElement) { // Check if button and modal element exist
            deleteSelectedBtn.addEventListener('click', function () {
                const checkedDevices = document.querySelectorAll('#delete-devices-form .delete-checkbox:checked');
                if (checkedDevices.length > 0) {
                    confirmDeleteModal.show(); // Use Bootstrap's show method
                } else {
                    // Reemplazado alert() con un modal de mensaje simple
                    const messageModalContent = document.querySelector('#confirmDeleteModal .modal-body');
                    const messageModalTitle = document.querySelector('#confirmDeleteModal .modal-title');
                    const messageModalFooter = document.querySelector('#confirmDeleteModal .modal-footer');

                    if (messageModalContent && messageModalTitle && messageModalFooter) {
                        messageModalTitle.textContent = "Atención";
                        messageModalContent.textContent = 'Por favor, selecciona al menos un dispositivo para eliminar.';
                        // Ocultar botones de acción y mostrar solo el de cerrar si no hay selección
                        messageModalFooter.innerHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
                        confirmDeleteModal.show();
                    } else {
                         // Fallback si los elementos del modal no se encuentran
                        console.error("No se pudieron encontrar elementos del modal de mensaje.");
                         // Evita alert() en producción
                    }
                }
            });
        }


        // Enviar el formulario de eliminación cuando se confirma en el modal
        if (confirmDeleteBtn && deleteDevicesForm && confirmDeleteModalElement) { // Check if all elements exist
            confirmDeleteBtn.addEventListener('click', function () {
                confirmDeleteModal.hide(); // Use Bootstrap's hide method
                deleteDevicesForm.submit();
            });
        }

    });
</script>
<?= $this->endSection() ?>