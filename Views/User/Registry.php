
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
                        <label for="email" class="form-label">Email</label>
                        <input autocomplete="off" type="text" name="email" id="email" class="form-control" value="<?php echo $JData->email; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input autocomplete="off" type="text" name="password" id="password" class="form-control" value="<?php echo $JData->password; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripcion</label>
                        <input autocomplete="off" type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $JData->descripcion; ?>">
                    </div> <br>

                    <div class="form-group">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="Active" <?php if($JData->estado == 'Active'){ echo "selected"; } ?>>Activo</option>
                            <option value="Inactive" <?php if($JData->estado == 'Inactive'){ echo "selected"; } ?>>Inactivo</option>
                        </select>
                    </div> <br>

                    <div class="form-group">
                        <label for="creacion" class="form-label">Creacion</label>
                        <input autocomplete="off" type="text" name="creacion" id="creacion" class="form-control" value="<?php echo $JData->creacion; ?>">
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
        const email = document.getElementById('email')?.value?.trim() || '';
        const password = document.getElementById('password')?.value?.trim() || '';
        const descripcion = document.getElementById('descripcion')?.value?.trim() || '';
        const estado = document.getElementById('estado')?.value?.trim() || '';
        const creacion = document.getElementById('creacion')?.value?.trim() || '';

        if (!email) {
            await Swal.fire('Error', 'El campo email  es obligatorio.', 'error');
            document.getElementById('email')?.focus();
            return;
        }
        if (!password) {
            await Swal.fire('Error', 'El campo Contraseña es obligatorio.', 'error');
            document.getElementById('password')?.focus();
            return;
        }
        if (!descripcion) {
            await Swal.fire('Error', 'El campo Descripcion es obligatorio.', 'error');
            document.getElementById('descripcion')?.focus();
            return;
        }
        if (!estado) {
            await Swal.fire('Error', 'El campo Estado es obligatorio.', 'error');
            document.getElementById('estado')?.focus();
            return;
        }
        if (!creacion) {
            await Swal.fire('Error', 'El campo Creacion es obligatorio.', 'error');
            document.getElementById('creacion')?.focus();
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