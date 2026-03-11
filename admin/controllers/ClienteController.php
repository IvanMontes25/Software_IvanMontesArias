<?php
require_once __DIR__ . '/BaseController.php';

class ClienteController extends BaseController
{
    

    public function index(): void
    {
        require_once __DIR__ . '/../../includes/membership_helper.php';

        // AJAX request
        if (isset($_GET['ajax'])) {
            $this->ajaxListar();
            return;
        }

        $this->render('clientes/index', [
            'page' => 'members',
            'pageTitle' => 'Clientes',
        ]);
    }

    private function ajaxListar(): void
    {
        $model = $this->model('ClienteModel');
        $allRows = $model->listarConMembresia();

        $q = trim($_GET['q'] ?? '');
        $filtro = trim($_GET['estado'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 35;
        $offset = ($page - 1) * $perPage;
        $today = new DateTime('today');

        $filtered = [];
        foreach ($allRows as $r) {
            // Calculate estado
            $estadoCalc = 'sin_membresia';
            $estadoText = 'Sin membresía';
            $estadoBadge = '<span class="badge badge-secondary">Sin membresía</span>';
            $planNombre = $r['plan_nombre'] ?: 'Sin membresía';

            if ($r['plan_nombre'] && $r['duracion_dias'] > 0) {
                $startRaw = $r['start_date'] ?: $r['paid_date'];
                try {
                    $startDate = new DateTime($startRaw);
                    $endDate = clone $startDate;
                    $endDate->modify("+{$r['duracion_dias']} days");
                    $daysLeft = (int)$today->diff($endDate)->format('%r%a');

                    if ($daysLeft < 0) {
                        $estadoCalc = 'vencida'; $estadoText = 'Vencida';
                        $estadoBadge = '<span class="badge badge-danger">Vencida</span>';
                    } elseif ($daysLeft <= 3) {
                        $estadoCalc = 'por_vencer'; $estadoText = 'Por vencer';
                        $estadoBadge = '<span class="badge badge-warning">Por vencer</span>';
                    } else {
                        $estadoCalc = 'activa'; $estadoText = 'Activa';
                        $estadoBadge = '<span class="badge badge-success">Activa</span>';
                    }
                } catch (Exception $e) {}
            }

            // Search filter
            $busqueda = strtolower($q);
            $campos = [strtolower($r['fullname']), strtolower($r['username']),
                       strtolower($r['ci']), strtolower($r['gender']),
                       strtolower($planNombre), strtolower($estadoText)];
            $coincide = true;
            if ($busqueda !== '') {
                $coincide = false;
                foreach ($campos as $campo) {
                    if (strpos($campo, $busqueda) !== false) { $coincide = true; break; }
                }
            }
            if ($filtro !== '' && $estadoCalc !== $filtro) $coincide = false;
            if (!$coincide) continue;

            $filtered[] = ['r' => $r, 'estadoBadge' => $estadoBadge, 'planNombre' => $planNombre];
        }

        $total = count($filtered);
        $totalPages = (int)ceil($total / $perPage);
        $slice = array_slice($filtered, $offset, $perPage);
        $rows = [];

        foreach ($slice as $idx => $item) {
            $r = $item['r'];
            $uid = (int)$r['user_id'];
            $num = $offset + $idx + 1;
            $rows[] = "<tr>
              <td class='text-muted'>$num</td>
              <td>" . $this->e($r['fullname']) . "</td>
              <td class='text-center'><span class='badge badge-username'>@" . $this->e($r['username']) . "</span></td>
              <td class='text-center'>" . $this->e($r['gender']) . "</td>
              <td class='text-center'><span class='phone-muted'>" . $this->e($r['contact']) . "</span></td>
              <td class='text-center'>" . $this->e($r['dor']) . "</td>
              <td class='text-center'>" . $this->e($r['ci']) . "</td>
              <td class='text-center'><span class='badge badge-service'>{$item['planNombre']}</span></td>
              <td class='text-center'>{$item['estadoBadge']}</td>
              <td class='text-center'>
                <div class='btn-group btn-group-sm'>
                  <a href='perfil_cliente.php?id=$uid' class='btn btn-outline-info'><i class='fas fa-user'></i></a>
                  <a href='logros_cliente.php?id=$uid' class='btn btn-outline-success'><i class='fas fa-trophy'></i></a>
                  <button class='btn btn-outline-primary btn-edit' data-id='$uid'><i class='fas fa-edit'></i></button>
                  <button class='btn btn-outline-danger btn-delete' data-id='$uid'><i class='fas fa-trash'></i></button>
                </div>
              </td>
            </tr>";
        }

        $this->json([
            'html' => $rows ? implode('', $rows) : "<tr><td colspan='10' class='text-center text-muted'>Sin registros</td></tr>",
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
        ]);
    }

    public function actions(): void
    {
        $this->requirePost();
        // CSRF is handled by header.php in the layout

        $model = $this->model('ClienteModel');
        $op = $_POST['op'] ?? '';

        if ($op === 'create') { $this->create($model); }
        elseif ($op === 'update') { $this->update($model); }
        elseif ($op === 'delete') { $this->delete($model); }
    }

    private function create(object $model): void
    {
        $data = [
            'fullname' => trim($_POST['fullname'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'dor'      => trim($_POST['dor'] ?? ''),
            'gender'   => trim($_POST['gender'] ?? ''),
            'ci'       => trim($_POST['ci'] ?? ''),
            'correo'   => trim($_POST['correo'] ?? ''),
            'contact'  => trim($_POST['contact'] ?? ''),
        ];

        // Validations
        if (strlen($data['fullname']) < 3) { $this->flash('client_error', 'El nombre debe tener al menos 3 caracteres.'); $this->redirect('cliente_entry.php'); }
        if (strlen($data['username']) < 3 || preg_match('/\s/', $data['username'])) { $this->flash('client_error', 'Usuario inválido.'); $this->redirect('cliente_entry.php'); }
        if (strlen($data['password']) < 6) { $this->flash('client_error', 'La contraseña debe tener mínimo 6 caracteres.'); $this->redirect('cliente_entry.php'); }
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) { $this->flash('client_error', 'Correo inválido.'); $this->redirect('cliente_entry.php'); }

        // Check duplicates
        $fields = [
            'username' => ['value' => $data['username'], 'label' => 'Usuario'],
            'ci' => ['value' => $data['ci'], 'label' => 'CI'],
            'correo' => ['value' => $data['correo'], 'label' => 'Correo electrónico'],
        ];
        if ($data['contact'] !== '') $fields['contact'] = ['value' => $data['contact'], 'label' => 'Teléfono'];

        $errors = $model->checkDuplicates($fields);
        if (!empty($errors)) {
            $this->flash('client_error', implode(' | ', $errors));
            $this->redirect('cliente_entry.php');
        }

        $lastId = $model->crear($data);
        if ($lastId) {
            require_once __DIR__ . '/../../core/audit.php';
            registrar_auditoria($this->db, 'crear_cliente', "Inscribió al cliente {$data['fullname']} (ID $lastId)", 'clientes');
            $this->flash('client_success', 'Cliente registrado correctamente.');
            $this->redirect("pago_cliente.php?id=$lastId");
        }

        $this->flash('client_error', 'Error al registrar cliente.');
        $this->redirect('cliente_entry.php');
    }

    private function update(object $model): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'fullname' => trim($_POST['fullname'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'gender'   => trim($_POST['gender'] ?? ''),
            'ci'       => trim($_POST['ci'] ?? ''),
            'contact'  => trim($_POST['contact'] ?? ''),
            'correo'   => trim($_POST['correo'] ?? ''),
        ];

        if ($id <= 0 || !$model->exists($id)) {
            $this->flash('edit_error', 'Cliente no encontrado.');
            $this->redirect('clientes.php');
        }

        if (strlen($data['fullname']) < 3) { $this->flash('edit_error', 'El nombre debe tener al menos 3 caracteres.'); $this->redirect("edit_clienteform.php?id=$id"); }
        if (strlen($data['username']) < 3) { $this->flash('edit_error', 'El usuario debe tener mínimo 3 caracteres.'); $this->redirect("edit_clienteform.php?id=$id"); }
        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) { $this->flash('edit_error', 'Correo no válido.'); $this->redirect("edit_clienteform.php?id=$id"); }

        $dups = $model->checkDuplicates(['username' => ['value' => $data['username'], 'label' => 'Usuario']], $id);
        if (!empty($dups)) { $this->flash('edit_error', $dups[0]); $this->redirect("edit_clienteform.php?id=$id"); }

        if ($model->actualizar($id, $data)) {
            require_once __DIR__ . '/../../core/audit.php';
            registrar_auditoria($this->db, 'editar_cliente', "Editó datos del cliente {$data['fullname']} (ID $id)", 'clientes');
            $this->flash('edit_success', 'Los datos del cliente se actualizaron correctamente.');
        } else {
            $this->flash('edit_error', 'No se pudo actualizar el cliente.');
        }
        $this->redirect("edit_clienteform.php?id=$id");
    }

    private function delete(object $model): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_POST['delete_id'] ?? 0);
        if ($id <= 0) { echo json_encode(['ok' => false, 'msg' => 'ID inválido']); exit; }

        if ($model->eliminar($id)) {
            require_once __DIR__ . '/../../core/audit.php';
            registrar_auditoria($this->db, 'eliminar_cliente', "Eliminó al cliente ID $id", 'clientes');
            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false, 'msg' => 'Error al eliminar']);
        }
        exit;
    }

    public function entry(): void
    {
        // Handle AJAX duplicate check
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_check_dup'])) {
            $model = $this->model('ClienteModel');
            $field = trim($_POST['field'] ?? '');
            $value = trim($_POST['value'] ?? '');
            $row = $model->checkFieldDuplicate($field, $value);
            $this->json([
                'duplicado' => $row !== null,
                'pertenece_a' => $row['fullname'] ?? null,
            ]);
        }

        $this->render('clientes/entry', ['page' => 'members-entry', 'pageTitle' => 'Inscribir Cliente']);
    }

    public function editForm(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $model = $this->model('ClienteModel');
        $row = $id > 0 ? $model->getForEdit($id) : null;

        $this->render('clientes/edit_form', [
            'page' => 'members-update',
            'pageTitle' => 'Editar Cliente',
            'row' => $row,
            'id' => $id,
        ]);
    }

    public function perfil(): void
    {
        require_once __DIR__ . '/../../includes/membership_helper.php';
        $model = $this->model('ClienteModel');

        $user_id = 0;
        if (isset($_GET['user_id']) && ctype_digit($_GET['user_id'])) $user_id = (int)$_GET['user_id'];
        elseif (isset($_GET['id']) && ctype_digit($_GET['id'])) $user_id = (int)$_GET['id'];
        if ($user_id <= 0) exit('ID inválido');

        $cli = $model->getById($user_id);
        if (!$cli) exit('Cliente no encontrado');

        $membresia = membership_last($this->db, $user_id);

        $this->render('clientes/perfil', [
            'page' => 'members',
            'pageTitle' => 'Perfil de Cliente',
            'cli' => $cli,
            'user_id' => $user_id,
            'membresia' => $membresia,
        ]);
    }

    public function historial(): void
    {
        require_once __DIR__ . '/../../includes/membership_helper.php';
        require_once __DIR__ . '/../../includes/payments_dao.php';
        $model = $this->model('ClienteModel');

        $user_id = (isset($_GET['user_id']) && ctype_digit($_GET['user_id'])) ? (int)$_GET['user_id'] : 0;
        if ($user_id <= 0) die("<div class='alert alert-danger text-center'>ID de cliente no válido.</div>");

        $member = $this->db->prepare("SELECT m.user_id, m.fullname, m.ci FROM members m WHERE m.user_id = ?");
        $member->bind_param('i', $user_id);
        $member->execute();
        $memberData = $member->get_result()->fetch_assoc();
        $member->close();

        $this->render('clientes/historial', [
            'page' => 'members',
            'pageTitle' => 'Historial',
            'user_id' => $user_id,
            'member' => $memberData,
        ]);
    }
}
