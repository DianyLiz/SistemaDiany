
<h1>Usuarios</h1>

<div class="card mb-grid">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-header-title">Lista de Usuarios</div>

        <div class="pulleft">
            <a href='/User/Registry/' type="button" class="btn btn-sm btn-primary">Registrar Usuario</a>

            <a href='/Report/UserReport' target="_blank" class="btn btn-sm btn-secondary me-2">
                <i class="fa fa-file-pdf"></i> Generar Reporte PDF
            </a>
        </div>
    </div>

    <div class="table-responsive-md">
        <table class="table table-actions table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th width="12%">Usuario</th>
                    <th>Nombre Completo</th>
                    <th width="15%">Correo</th>
                    <th width="10%">Teléfono</th>
                    <th width="15%">Servicio</th>
                    <th width="12%">Rol</th>
                    <th width="10%">Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($JData as $Key => $Value)
                {
                    $serviceName = isset($serviceMap[$Value->service_id]) ? $serviceMap[$Value->service_id] : $Value->service_id;
                    $roleName = isset($roleMap[$Value->role_id]) ? $roleMap[$Value->role_id] : $Value->role_id;

                    echo "<tr>";
                        echo "<td>".$Value-> username."</td>";
                        echo "<td>".$Value-> name."</td>";
                        echo "<td>".$Value-> email."</td>";
                        echo "<td>".$Value-> phone."</td>";
                        echo "<td>".htmlspecialchars($serviceName)."</td>";
                        echo "<td>".htmlspecialchars($roleName)."</td>";
                        echo "<td>";
                            if($Value-> status == 'Active'){
                                echo "<span class='badge bg-success'>Activo</span>";
                            } else {
                                echo "<span class='badge bg-danger'>Inactivo</span>";
                            }
                            echo "</td>";
                        echo "<td>
                        <a href='/User/Registry/".$Value->id."' class='btn btn-sm btn-primary'>Editar</a>
                        <a href='javascript:eliminar(".$Value->id.");' class='btn btn-sm btn-secondary'>Eliminar</a>
                        </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
   function eliminar(id) {
    Swal.fire({
        title: "¿Está seguro?",
        text: "¡No podrá revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#b70124",
        cancelButtonColor: "#5c636a",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (!result.isConfirmed) return;

        var data = { id: id, table: 'user'};

        $.ajax({
            url: "/API?method=Delete",
            method: "POST",
            data: data,
            dataType: "json"
        }).done(function(res) {
            if (res && res.success) {
                Swal.fire("Eliminado", res.message || "Registro eliminado correctamente.", "success")
                    .then(() => location.reload());
            } else {
                Swal.fire("Error", res?.message || "Error al eliminar el registro.", "error");
            }
        }).fail(function(xhr) {
            console.error("API error:", xhr);
            const msg = xhr.responseJSON?.message || xhr.responseText || "Error en la petición";
            Swal.fire("Error", msg, "error");
        });
    });
}
</script>