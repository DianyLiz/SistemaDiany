<?php
    use Models\Service as Service;
    use Models\Role as Role;

    $services = new Service();
    $services = $services->toList();

    $roles = new Role();
    $roles = $roles->toList();
    //echo json_encode($roles);
?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-grid">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-header-title"><h3>Registro de Usuario</h3></div>
            </div>

            <div class="card-body collapse show">
                <form id="userForm" action="" method="POST">

                    <input type="hidden" name="Registrar" id="Registrar" value="1">

                    <div class="form-group">
                        <input  require type="hidden" name="id" id="id" class="form-control" readonly value="<?php echo $JData->id; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input autocomplete="off" type="text" name="name" id="name" class="form-control" value="<?php echo $JData->name; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="username" class="form-label">Usuario</label>
                        <input autocomplete="off" type="text" name="username" id="username" class="form-control" value="<?php echo $JData->username; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <input autocomplete="off" type="password" name="password" id="password" class="form-control" value="<?php echo $JData->password; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="service_id" class="form-label">Servicio</label>
                        <select name="service_id" id="service_id"  class="form-select">
                            <option value="" default="selected">-- SELECCIONE UNA OPCIÓN --</option>
                            <?php
                                foreach($services as $key => $value) {
                                    $selected = ($JData->service_id == $value->id) ? 'selected' : '';
                                    echo "<option value='".$value->id."' $selected>".$value->name."</option>";
                                }
                            ?>
                        </select>
                    </div> <br>

                    <div class="form-group">
                        <label for="role_id" class="form-label">Rol</label>
                        <select name="role_id" id="role_id" class="form-select">
                            <option value="" default="selected">-- SELECCIONE UNA OPCIÓN --</option>
                            <?php
                                foreach($roles as $key => $value) {
                                    $selected = ($JData->role_id == $value->id) ? 'selected' : '';
                                    echo "<option value='".$value->id."' $selected>".$value->name."</option>";
                                }
                            ?>
                        </select>
                    </div> <br>

                    <div class="form-group">
                        <label for="email" class="form-label">Correo</label>
                        <input autocomplete="off" type="text" name="email" id="email" class="form-control" value="<?php echo $JData->email; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input autocomplete="off" type="text" name="phone" id="phone" class="form-control" value="<?php echo $JData->phone; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="status" class="form-label">Estado</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Active" <?php if($JData->status == 'Active'){ echo "selected"; } ?>>Activo</option>
                            <option value="Inactive" <?php if($JData->status == 'Inactive'){ echo "selected"; } ?>>Inactivo</option>
                        </select>
                    </div> <br>

                    <div class="form-group">
                        <a href="/User" class="btn btn-secondary">Regresar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const form = document.getElementById('userForm');
    if (!form) return;

    form.addEventListener('submit', async function(e){
        e.preventDefault();

        // VALIDACIÓN CLIENTE: campos obligatorios
        const name = document.getElementById('name')?.value?.trim() || '';
        const username = document.getElementById('username')?.value?.trim() || '';
        const password = document.getElementById('password')?.value?.trim() || '';
        const service_id = document.getElementById('service_id')?.value?.trim() || '';
        const role_id = document.getElementById('role_id')?.value?.trim() || '';
        const email = document.getElementById('email')?.value?.trim() || '';
        const phone = document.getElementById('phone')?.value?.trim() || '';

        if (!name) {
            await Swal.fire('Error', 'El campo Nombre Completo es obligatorio.', 'error');
            document.getElementById('name')?.focus();
            return;
        }
        if (!username) {
            await Swal.fire('Error', 'El campo Usuario es obligatorio.', 'error');
            document.getElementById('username')?.focus();
            return;
        }
        if (!password) {
            await Swal.fire('Error', 'El campo Contraseña es obligatorio.', 'error');
            document.getElementById('password')?.focus();
            return;
        }
        if (!service_id) {
            await Swal.fire('Error', 'El campo Servicio es obligatorio.', 'error');
            document.getElementById('service_id')?.focus();
            return;
        }
        if (!role_id) {
            await Swal.fire('Error', 'El campo Rol es obligatorio.', 'error');
            document.getElementById('role_id')?.focus();
            return;
        }
        if (!email) {
            await Swal.fire('Error', 'El campo Correo es obligatorio.', 'error');
            document.getElementById('email')?.focus();
            return;
        }
        if (!phone) {
            await Swal.fire('Error', 'El campo Teléfono es obligatorio.', 'error');
            document.getElementById('phone')?.focus();
            return;
        }

        const confirm = await Swal.fire({
            title: '¿Guardar cambios?',
            text: 'Se guardarán los datos del usuario.',
            icon: 'question',
            confirmButtonColor: "#b70124",
            cancelButtonColor: "#5c636a",
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        });
        if (!confirm.isConfirmed) return;

        const fd = new FormData(form);
        fd.append('ajax', '1');

        try {
            const res = await fetch(form.action || window.location.pathname, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            });

            const text = await res.text();
            let data = null;
            try { data = JSON.parse(text); } catch(e){ /* no JSON */ }

            if (res.ok) {
                const msg = data && data.message ? data.message : 'Usuario guardado correctamente.';
                await Swal.fire('Éxito', msg, 'success');
                window.location.href = (data && data.redirect) ? data.redirect : '/User';
            } else {
                const errMsg = (data && data.message) ? data.message : (text || 'Error en el servidor');
                Swal.fire('Error', errMsg, 'error');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            Swal.fire('Error', 'Error en la petición. Revisa la consola.', 'error');
        }
    });
})();
</script>