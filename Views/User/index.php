
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
                    <th width="12%">Email</th>
                    <th width="10%">Descripción</th>
                    <th width="10%">Estado</th>
                    <th width="15%">Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($JData as $Key => $Value)
                {

                    echo "<tr>";
                        echo "<td>".htmlspecialchars($Value-> email, ENT_QUOTES, 'UTF-8')."</td>";
                        echo "<td>".htmlspecialchars($Value-> descripcion, ENT_QUOTES, 'UTF-8')."</td>";
                        echo "<td>".htmlspecialchars($Value-> estado, ENT_QUOTES, 'UTF-8')."</td>";
                        echo "<td>".htmlspecialchars($Value-> creacion, ENT_QUOTES, 'UTF-8')."</td>";
                        echo "<td>";
                            if($Value-> estado == 'Active'){
                                echo "<span class='badge bg-success'>Activo</span>";
                            } else {
                                echo "<span class='badge bg-danger'>Inactivo</span>";
                            }
                            echo "</td>";
                        echo "<td>
                            <a href='/User/Detalle/".$Value->id."' class='btn btn-sm btn-primary'>Detalle</a>
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

        var data = { id: id, table: 'Users'};

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