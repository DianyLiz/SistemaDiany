
<div class="row">
    <div class="col-md-6">
        <div class="card mb-grid">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-header-title"><h3>Detalle de Usuario</h3></div>
            </div>

            <div class="card-body collapse show">
                <form>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" id="email" class="form-control" value="<?php echo htmlspecialchars($JData->email ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" id="password" class="form-control" value="<?php echo htmlspecialchars($JData->password ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    </div>

                    <div class="form-group mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <input type="text" id="descripcion" class="form-control" value="<?php echo htmlspecialchars($JData->descripcion ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    </div>

                    <div class="form-group mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" id="estado" class="form-control" value="<?php echo htmlspecialchars($JData->estado ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    </div>

                    <div class="form-group mb-4">
                        <label for="creacion" class="form-label">Creación</label>
                        <input type="text" id="creacion" class="form-control" value="<?php echo htmlspecialchars($JData->creacion ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <a href="/User" class="btn btn-secondary">Regresar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
