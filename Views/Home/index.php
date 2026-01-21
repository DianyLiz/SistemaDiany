
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1">Panel de Control</h1>
                    <p class="text-muted">Bienvenido al sistema de gestión de tickets</p>
                </div>
                <div>
                    <a href="Views/selection/index.html" target="_blank" class="btn btn-primary">Panel Cliente</a>
                    <a href=""></a>
                    <a href="Views/ticket-display/index.html?admin=1" target="_blank" class="btn btn-primary">Panel General</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="row">
        <!-- Tablas de Tickets por categoría (muestra ticket y caja que los atiende) -->
        <div class="col-12 mb-4">
            <?php
                // Obtener tickets desde la base de datos y clasificarlos para el panel
                $normalTickets = [];
                $customerTickets = [];
                $premiumTickets = [];

                try {
                    $pdo = new \Config\Conexion();
                    $pdo = $pdo->getConexion();

                        // Obtener tickets en cola: buscar por nombre de estado que contenga 'espera'
                        // (más robusto que asumir un id fijo, evita discrepancias entre entornos)
                        $sql = "SELECT t.id, t.ticket_code, s.name AS service_name, ct.name AS client_type_name, ts.name AS status_name
                            FROM Tickets t
                            LEFT JOIN Services s ON s.id = t.service_id
                            LEFT JOIN ClientTypes ct ON ct.id = t.client_type_id
                            LEFT JOIN TicketStatuses ts ON ts.id = t.status_id
                            WHERE LOWER(COALESCE(ts.name, '')) LIKE '%espera%'
                            ORDER BY t.id DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $all = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    foreach ($all as $row) {
                        $id = $row['id'];
                        $ticket_code = isset($row['ticket_code']) ? $row['ticket_code'] : $id;
                        $serviceName = isset($row['service_name']) ? $row['service_name'] : '';
                        $clientTypeName = isset($row['client_type_name']) ? $row['client_type_name'] : '';

                        // Clasificación heurística (ajustable según datos reales)
                        $ctLower = strtolower($clientTypeName);
                        $sLower = strtolower($serviceName);

                        // keep numeric id and show ticket_code separately
                        $item = ['id' => $id, 'code' => $ticket_code, 'caja' => $serviceName];

                        if (strpos($ctLower, 'prefer') !== false || strpos($ctLower, 'preferencial') !== false) {
                            $premiumTickets[] = $item;
                        } elseif (strpos($sLower, 'atenc') !== false || strpos($sLower, 'cliente') !== false || strpos($ctLower, 'atenc') !== false) {
                            $customerTickets[] = $item;
                        } else {
                            $normalTickets[] = $item;
                        }
                    }
                } catch (\Exception $e) {
                    // En caso de error con la BD, dejar arrays vacíos pero registrar para depuración
                    @file_put_contents(__DIR__ . '/../../APIR/logs/home_errors.log', '['.date('Y-m-d H:i:s').'] Home list error: '.$e->getMessage()."\n", FILE_APPEND);
                }
            ?>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Normal</h6>
                                    <h3 class="mb-0"><?php echo count($normalTickets); ?></h3>
                                    <small class="text-muted">Tickets en cola</small>
                                </div>
                                <div class="stat-icon bg-primary"><i class="fas fa-ticket-alt"></i></div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ticket</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($normalTickets as $tk){ ?>
                                            <tr>
                                                <td><strong>#<?php echo $tk['code']; ?></strong></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success tomar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>" data-caja="<?php echo $tk['caja']; ?>">Tomar</button>
                                                    <button type="button" class="btn btn-sm btn-danger cerrar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>">Cerrar</button>
                                                    <button type="button" class="btn btn-sm btn-warning cambiar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>">Cambiar Servicio</button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary repetir-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>" disabled>Repetir</button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Atención al Cliente</h6>
                                    <h3 class="mb-0"><?php echo count($customerTickets); ?></h3>
                                    <small class="text-muted">Tickets en cola</small>
                                </div>
                                <div class="stat-icon bg-warning"><i class="fas fa-headset"></i></div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ticket</th>
                                            <th>Caja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($customerTickets as $tk){ ?>
                                            <tr>
                                                <td><strong>#<?php echo $tk['code']; ?></strong></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success tomar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>" data-caja="<?php echo $tk['caja']; ?>">Tomar</button>
                                                    <button type="button" class="btn btn-sm btn-danger cerrar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>">Cerrar</button>
                                                    <button type="button" class="btn btn-sm btn-warning cambiar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>">Cambiar Servicio</button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary repetir-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>" disabled>Repetir</button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card card-stat">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Preferencial</h6>
                                    <h3 class="mb-0"><?php echo count($premiumTickets); ?></h3>
                                    <small class="text-muted">Tickets en cola</small>
                                </div>
                                <div class="stat-icon bg-info"><i class="fas fa-star"></i></div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ticket</th>
                                            <th>Caja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($premiumTickets as $tk){ ?>
                                            <tr>
                                                <td><strong>#<?php echo $tk['code']; ?></strong></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success tomar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>" data-caja="<?php echo $tk['caja']; ?>">Tomar</button>
                                                    <button type="button" class="btn btn-sm btn-danger cerrar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>">Cerrar</button>
                                                    <button type="button" class="btn btn-sm btn-warning cambiar-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>">Cambiar Servicio</button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary repetir-btn" data-id="<?php echo $tk['id']; ?>" data-code="<?php echo $tk['code']; ?>" disabled>Repetir</button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Regreso de estilos: reglas específicas para Home (anteriormente en custom-themes.css) -->
<style>
/* ----------------------------- */
/* Estilos para Home: tickets y tablas */
/* ----------------------------- */

/* Card stat refinada */
.card-stat {
    background: var(--card-bg, #fff);
    border: 1px solid rgba(0,0,0,0.04);
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(14,30,37,0.06);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.card-stat:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(14,30,37,0.08); }

.card-stat .card-body { padding: 1rem; }

.stat-icon { width:46px; height:46px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; color:#fff; }
.stat-icon.bg-primary { background: linear-gradient(135deg, var(--primary-color,#0d6efd), var(--primary-hover,#0b5ed7)); }
.stat-icon.bg-warning { background: linear-gradient(135deg, #ffb74d, #ff9800); }
.stat-icon.bg-info { background: linear-gradient(135deg, #20c997, #0dcaf0); }

/* Small tables inside stat cards */
.card-stat .table-sm { margin-bottom: 0; }
.card-stat .table-sm td, .card-stat .table-sm th { padding: .45rem .6rem; vertical-align: middle; }
.card-stat .table-sm thead th { font-weight:600; font-size:0.85rem; color:var(--text-color, #212529); border-bottom: none; }
.card-stat .table-sm tbody td { font-size:0.95rem; border-top: 1px dashed rgba(0,0,0,0.04); }
.card-stat .table-sm tbody tr:hover { background: rgba(13,110,253,0.03); }

/* Ticket small (kept for backward compatibility) */
.ticket-small { display:inline-flex; align-items:center; justify-content:center; min-width:72px; height:46px; border-radius:8px; padding:.35rem .6rem; box-shadow:0 2px 6px rgba(0,0,0,0.04); font-weight:700; transition:transform .18s ease, box-shadow .18s ease; }
.ticket-small:hover { transform:translateY(-3px); box-shadow:0 6px 14px rgba(0,0,0,0.08); }
.ticket-number-small { font-size:1rem; color:inherit; }

.ticket-small.ticket-normal { background:linear-gradient(135deg, #f0f7ff, #e5f0ff); color:#0d6efd; border-left:3px solid rgba(13,110,253,0.14); }
.ticket-small.ticket-customer { background:linear-gradient(135deg,#fff9f0,#fff3e6); color:#f57c00; border-left:3px solid rgba(245,124,0,0.14); }
.ticket-small.ticket-premium { background:linear-gradient(135deg,#fbf5ff,#f1e7fb); color:#7b1fa2; border-left:3px solid rgba(123,31,162,0.14); }

@media (max-width:576px) { .ticket-small { min-width:64px; height:44px;} .ticket-number-small { font-size:.95rem; } }

/* Visual tweaks for ticket tables */
.card-stat .table-sm tbody tr td:first-child { font-weight:700; }
.card-stat .table-sm tbody tr td:last-child { color:var(--secondary-color,#6c757d); }

</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // expose current user id and name to JS so we can emit which servicedesk/user took the ticket
    window.CURRENT_USER_ID = <?php echo isset($_SESSION['system']['user_id']) ? json_encode($_SESSION['system']['user_id']) : 'null'; ?>;
    window.CURRENT_USER_NAME = <?php echo isset($_SESSION['system']['name']) ? json_encode($_SESSION['system']['name']) : 'null'; ?>;
    <?php
        // try to resolve the ServiceDesks.id for the current user so frontends can emit desk id instead of user id
        $CURRENT_DESK_ID = null;
        $CURRENT_DESK_NAME = null;
        if (isset($_SESSION['system']['user_id'])) {
            try {
                $__pdo = new \Config\Conexion();
                $__conn = $__pdo->getConexion();
                $__stmt = $__conn->prepare("SELECT id, desk_name FROM ServiceDesks WHERE user_id = :uid LIMIT 1");
                $__stmt->bindValue(':uid', $_SESSION['system']['user_id']);
                $__stmt->execute();
                $__row = $__stmt->fetch(PDO::FETCH_ASSOC);
                if ($__row && isset($__row['id'])) $CURRENT_DESK_ID = $__row['id'];
                if ($__row && isset($__row['desk_name'])) $CURRENT_DESK_NAME = $__row['desk_name'];
            } catch (Exception $e) {
                $CURRENT_DESK_ID = null;
            }
        }
    ?>
    window.CURRENT_DESK_ID = <?php echo json_encode($CURRENT_DESK_ID); ?>;
    window.CURRENT_DESK_NAME = <?php echo json_encode($CURRENT_DESK_NAME); ?>;

    (function(){
        function apiUpdate(data){
            // include API credentials if needed when not logged in
            var payload = Object.assign({}, data);
            // If session does not exist for kiosk, you can pass uid/pw here. Otherwise omitted.
            return $.ajax({
                url: '/APIR/index.php?method=update_ticket',
                method: 'POST',
                data: payload,
                dataType: 'json'
            });
        }

        // Handle 'Tomar' without reloading: mark row as in-attention so employee can Close or Change
        $(document).on('click', '.tomar-btn', function(){
            var $btn = $(this);
            var id = $btn.data('id');
            var code = $btn.data('code') || '';
            Swal.fire({
                title: 'Confirmar',
                text: '¿Tomar ticket ' + id + ' para atención? (Se quedará en su pantalla hasta cerrar)',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, tomar',
                cancelButtonText: 'Cancelar'
            }).then(function(result){
                if(!result.isConfirmed) return;
                apiUpdate({ id: id, action: 'take' })
                .done(function(res){
                    if(res && res.success){
                        var payload = { event: 'ticket_changed', action: 'take', id: id, code: code || res.code || '', serviceDeskId: window.CURRENT_DESK_ID, serviceDeskUserId: window.CURRENT_USER_ID, serviceDeskName: window.CURRENT_DESK_NAME || null, ts: Date.now() };
                        try{ localStorage.setItem('ticket_event', JSON.stringify(payload)); }catch(e){}
                        try{ if('BroadcastChannel' in window){ (new BroadcastChannel('ticket_events')).postMessage(payload); } }catch(e){}

                        var $row = $btn.closest('tr');
                        // Disable tomar visually and keep row for actions
                        $btn.prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                        $row.find('.cerrar-btn').prop('disabled', false).removeClass('disabled');
                        $row.find('.cambiar-btn').prop('disabled', false).removeClass('disabled');
                            $row.find('.repetir-btn').prop('disabled', false).removeClass('disabled');
                        $row.attr('data-taken-by', window.CURRENT_USER_ID);
                        if($row.find('.badge-attention').length === 0){
                            $row.find('td:first-child').append(' <span class="badge bg-info ms-2 badge-attention">En atención</span>');
                        }
                        Swal.fire({ icon: 'success', title: 'Ticket tomado', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire('Error', res.message || 'Error actualizando ticket', 'error');
                    }
                }).fail(function(xhr){
                    Swal.fire('Error', 'Error en la petición: ' + (xhr.responseJSON?.message || xhr.responseText || xhr.statusText), 'error');
                });
            });
        });

        // Close: remove row and emit event
        $(document).on('click', '.cerrar-btn', function(){
            var $btn = $(this);
            var id = $btn.data('id');
            var code = $btn.data('code') || '';
            Swal.fire({
                title: 'Confirmar cierre',
                text: '¿Cerrar ticket ' + id + '? Esta acción finalizará la gestión.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar',
                cancelButtonText: 'Cancelar'
            }).then(function(result){
                if(!result.isConfirmed) return;
                apiUpdate({ id: id, action: 'close' })
                .done(function(res){
                    if(res && res.success){
                        var payload = { event: 'ticket_changed', action: 'close', id: id, code: code || res.code || '', serviceDeskId: window.CURRENT_DESK_ID, serviceDeskUserId: window.CURRENT_USER_ID, serviceDeskName: window.CURRENT_DESK_NAME || null, ts: Date.now() };
                        try{ localStorage.setItem('ticket_event', JSON.stringify(payload)); }catch(e){}
                        try{ if('BroadcastChannel' in window){ (new BroadcastChannel('ticket_events')).postMessage(payload); } }catch(e){}
                        $btn.closest('tr').remove();
                        Swal.fire({ icon: 'success', title: 'Ticket cerrado', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire('Error', res.message || 'Error cerrando ticket', 'error');
                    }
                }).fail(function(xhr){
                    Swal.fire('Error', 'Error en la petición: ' + (xhr.responseJSON?.message || xhr.responseText || xhr.statusText), 'error');
                });
            });
        });

        // Change service: keep row visible so employee can continue managing
        $(document).on('click', '.cambiar-btn', function(){
            var $btn = $(this);
            var id = $btn.data('id');
            var code = $btn.data('code') || '';
            Swal.fire({
                title: 'Cambiar servicio',
                input: 'text',
                inputLabel: 'Ingrese el ID del servicio destino (ej. 1 para Caja, 2 para Atención):',
                inputPlaceholder: 'ID del servicio',
                showCancelButton: true,
                confirmButtonText: 'Cambiar',
                cancelButtonText: 'Cancelar'
            }).then(function(result){
                if(!result.isConfirmed) return;
                var newService = (result.value || '').toString().trim();
                if (newService === '' || isNaN(newService)) { Swal.fire('Error', 'ID de servicio inválido', 'error'); return; }
                Swal.fire({ title: 'Confirmar', text: 'Cambiar servicio del ticket '+id+' al servicio id '+newService+'?', icon: 'question', showCancelButton:true, confirmButtonText:'Sí, cambiar', cancelButtonText:'Cancelar' }).then(function(c){
                    if(!c.isConfirmed) return;
                    apiUpdate({ id: id, action: 'change_service', service_id: newService })
                    .done(function(res){
                        if(res && res.success){
                            var payload = { event: 'ticket_changed', action: 'change_service', id: id, newService: newService, code: code || res.code || '', ts: Date.now(), serviceDeskId: window.CURRENT_DESK_ID, serviceDeskUserId: window.CURRENT_USER_ID, serviceDeskName: window.CURRENT_DESK_NAME || null };
                            try{ localStorage.setItem('ticket_event', JSON.stringify(payload)); }catch(e){}
                            try{ if('BroadcastChannel' in window){ (new BroadcastChannel('ticket_events')).postMessage(payload); } }catch(e){}
                            var $row = $btn.closest('tr');
                            if($row.find('.badge-service').length === 0){
                                $row.find('td:first-child').append(' <span class="badge bg-secondary ms-2 badge-service">S:'+newService+'</span>');
                            } else {
                                $row.find('.badge-service').text('S:'+newService);
                            }
                            Swal.fire({ icon: 'success', title: 'Servicio cambiado', toast:true, position:'top-end', timer:1500, showConfirmButton:false });
                        } else {
                            Swal.fire('Error', res.message || 'Error cambiando servicio', 'error');
                        }
                    }).fail(function(xhr){
                        Swal.fire('Error', 'Error en la petición: ' + (xhr.responseJSON?.message || xhr.responseText || xhr.statusText), 'error');
                    });
                });
            });
        });
        
        // Sync listener: respond to events from other Home tabs
        function handleExternalEvent(payload){
            try{
                if(!payload || payload.event !== 'ticket_changed') return;
                var id = payload.id;
                var action = payload.action;
                var originSD = payload.serviceDeskId || payload.service_desk_id || null;
                console.log('Home: external event', payload);

                // Ignore events originated from this desk for UI duplication
                if(originSD && originSD == window.CURRENT_DESK_ID) {
                    // still handle 'close' if needed
                    if(action === 'close'){
                        $('button.tomar-btn[data-id="'+id+'"]').closest('tr').remove();
                    }
                    return;
                }

                if(action === 'take'){
                    // mark the ticket as taken by another operator instead of removing it
                    var $btn = $('button.tomar-btn[data-id="'+id+'"]');
                    if($btn.length){
                        var $row = $btn.closest('tr');
                        // disable tomar for others
                        $btn.prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                        // disable cerrar/cambiar for other users
                        $row.find('.cerrar-btn').prop('disabled', true).addClass('disabled');
                        $row.find('.cambiar-btn').prop('disabled', true).addClass('disabled');
                        // set data-taken-by and show badge with name
                        $row.attr('data-taken-by', originSD || '');
                        var name = payload.serviceDeskName || originSD || 'Usuario';
                        if($row.find('.badge-taken-by').length === 0){
                            $row.find('td:first-child').append(' <span class="badge bg-warning ms-2 badge-taken-by">Tomado por: '+name+'</span>');
                        } else {
                            $row.find('.badge-taken-by').text('Tomado por: '+name);
                        }
                    }
                } else if(action === 'close'){
                    $('button.tomar-btn[data-id="'+id+'"], button.cerrar-btn[data-id="'+id+'"], button.cambiar-btn[data-id="'+id+'"]').closest('tr').remove();
                } else if(action === 'change_service'){
                    var $btn = $('button.tomar-btn[data-id="'+id+'"], button.cerrar-btn[data-id="'+id+'"], button.cambiar-btn[data-id="'+id+'"]').first();
                    if($btn.length){
                        var $row = $btn.closest('tr');
                        var svc = payload.newService || payload.service_id || '';
                        if($row.find('.badge-service').length === 0){
                            $row.find('td:first-child').append(' <span class="badge bg-secondary ms-2 badge-service">S:'+svc+'</span>');
                        } else {
                            $row.find('.badge-service').text('S:'+svc);
                        }
                    }
                }
            }catch(e){ console.warn('handleExternalEvent error', e); }
        }

        // Repetir llamado: cuando el empleado pide repetir el anuncio para un ticket
        $(document).on('click', '.repetir-btn', function(){
            var $btn = $(this);
            if($btn.prop('disabled')) return;
            var id = $btn.data('id');
            var code = $btn.data('code') || '';
            var payload = { event: 'ticket_changed', action: 'repeat', id: id, code: code, serviceDeskId: window.CURRENT_DESK_ID, serviceDeskUserId: window.CURRENT_USER_ID, serviceDeskName: window.CURRENT_DESK_NAME || null, ts: Date.now() };
            try{ localStorage.setItem('ticket_event', JSON.stringify(payload)); }catch(e){}
            try{ if('BroadcastChannel' in window){ (new BroadcastChannel('ticket_events')).postMessage(payload); } }catch(e){}
            Swal.fire({ icon: 'info', title: 'Repetición solicitada', toast:true, position:'top-end', timer:1200, showConfirmButton:false });
        });

        // storage listener (other tabs)
        window.addEventListener('storage', function(e){
            if(!e.key) return;
            if(e.key === 'ticket_event'){
                try{
                    var payload = JSON.parse(e.newValue || '{}');
                    handleExternalEvent(payload);
                }catch(err){ console.error('home storage parse error', err); }
            }
        });

        // BroadcastChannel listener
        try{
            if('BroadcastChannel' in window){
                const bcHome = new BroadcastChannel('ticket_events');
                bcHome.addEventListener('message', function(ev){
                    try{
                        var payload = ev.data;
                        // ignore messages originated in this same tab
                        if(payload && payload.serviceDeskId && payload.serviceDeskId == window.CURRENT_DESK_ID) return;
                        handleExternalEvent(payload);
                    }catch(err){ console.error('home bc msg err', err); }
                });
            }
        }catch(e){ console.warn('BroadcastChannel not available (home)', e); }
    })();
</script>