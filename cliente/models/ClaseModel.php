<?php
// cliente/models/ClaseModel.php
// Modelo: Gestión de clases y reservas para el cliente
// CORREGIDO: usa staffs.user_id y members.user_id

class ClaseModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    // =========================================================
    // CONSULTAS DE LECTURA
    // =========================================================

    /**
     * Obtener clases disponibles (futuras, con cupo, activas)
     */
    public function getDisponibles(?int $tipo_id = null, ?int $entrenador_id = null): array
    {
        $sql = "
            SELECT s.id, s.fecha, s.hora_inicio, s.hora_fin,
                   s.cupo_maximo, s.cupo_disponible, s.descripcion,
                   t.nombre AS tipo_nombre, t.color AS tipo_color,
                   st.fullname AS entrenador_nombre,
                   ROUND((1 - s.cupo_disponible / s.cupo_maximo) * 100) AS porcentaje_ocupacion
            FROM clases_sesiones s
            JOIN clase_tipos t ON t.id = s.tipo_clase_id
            JOIN staffs st ON st.user_id = s.entrenador_id
            WHERE s.estado = 'activa'
              AND s.cupo_disponible > 0
              AND CONCAT(s.fecha, ' ', s.hora_inicio) > NOW()
        ";
        $params = [];
        $types = '';

        if ($tipo_id) {
            $sql .= " AND s.tipo_clase_id = ?";
            $params[] = $tipo_id;
            $types .= 'i';
        }
        if ($entrenador_id) {
            $sql .= " AND s.entrenador_id = ?";
            $params[] = $entrenador_id;
            $types .= 'i';
        }

        $sql .= " ORDER BY s.fecha ASC, s.hora_inicio ASC";

        $st = $this->db->prepare($sql);
        if ($types) {
            $st->bind_param($types, ...$params);
        }
        $st->execute();
        $res = $st->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        $st->close();
        return $rows;
    }

    /**
     * Obtener las reservas de un cliente (historial completo)
     */
    public function getMisReservas(int $cliente_id): array
    {
        $sql = "
            SELECT r.id AS reserva_id, r.sesion_id, r.estado AS reserva_estado,
                   r.created_at AS fecha_reserva, r.posicion_espera,
                   s.fecha, s.hora_inicio, s.hora_fin,
                   t.nombre AS tipo_nombre, t.color AS tipo_color,
                   st.fullname AS entrenador_nombre,
                   s.estado AS sesion_estado
            FROM clases_reservas r
            JOIN clases_sesiones s ON s.id = r.sesion_id
            JOIN clase_tipos t ON t.id = s.tipo_clase_id
            JOIN staffs st ON st.user_id = s.entrenador_id
            WHERE r.cliente_id = ?
            ORDER BY s.fecha DESC, s.hora_inicio DESC
        ";
        $st = $this->db->prepare($sql);
        $st->bind_param("i", $cliente_id);
        $st->execute();
        $res = $st->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        $st->close();
        return $rows;
    }

    /**
     * Contar reservas activas (para limitar máximo N simultáneas)
     */
    public function contarReservasActivas(int $cliente_id): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM clases_reservas r
            JOIN clases_sesiones s ON s.id = r.sesion_id
            WHERE r.cliente_id = ?
              AND r.estado IN ('confirmada', 'en_espera')
              AND CONCAT(s.fecha, ' ', s.hora_inicio) > NOW()
        ";
        $st = $this->db->prepare($sql);
        $st->bind_param("i", $cliente_id);
        $st->execute();
        $r = $st->get_result()->fetch_assoc();
        $st->close();
        return (int) ($r['total'] ?? 0);
    }

    /**
     * Obtener tipos de clase activos (para filtros)
     */
    public function getTiposClase(): array
    {
        $res = $this->db->query("SELECT id, nombre, color FROM clase_tipos WHERE activo = 1 ORDER BY nombre");
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    /**
     * Obtener entrenadores activos (para filtros)
     */
    public function getEntrenadores(): array
    {
        $res = $this->db->query("
    SELECT DISTINCT st.user_id, st.fullname
    FROM staffs st
    WHERE LOWER(st.designation) LIKE '%entrenador%'
    ORDER BY st.fullname
");
        $rows = [];
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
        }
        return $rows;
    }

    // =========================================================
    // OPERACIONES TRANSACCIONALES
    // =========================================================

    /**
     * RESERVAR una clase (transacción atómica con FOR UPDATE)
     */
    public function reservar(int $sesion_id, int $cliente_id): array
    {
        // Límite de reservas activas simultáneas
        $MAX_RESERVAS = 5;
        $activas = $this->contarReservasActivas($cliente_id);
        if ($activas >= $MAX_RESERVAS) {
            return [
                'ok' => false,
                'msg' => "Ya tienes $activas reservas activas. El máximo es $MAX_RESERVAS. Cancela alguna para reservar otra."
            ];
        }

        $this->db->begin_transaction();

        try {
            // 1) Bloquear la sesión (FOR UPDATE evita race conditions)
            $st = $this->db->prepare("
                SELECT cupo_disponible, estado, fecha, hora_inicio
                FROM clases_sesiones
                WHERE id = ? FOR UPDATE
            ");
            $st->bind_param("i", $sesion_id);
            $st->execute();
            $sesion = $st->get_result()->fetch_assoc();
            $st->close();

            if (!$sesion || $sesion['estado'] !== 'activa') {
                $this->db->rollback();
                return ['ok' => false, 'msg' => 'Esta clase ya no está disponible.'];
            }

            // Verificar que la clase es futura
            $claseDt = $sesion['fecha'] . ' ' . $sesion['hora_inicio'];
            if (strtotime($claseDt) <= time()) {
                $this->db->rollback();
                return ['ok' => false, 'msg' => 'Esta clase ya comenzó o pasó.'];
            }

            // 2) Verificar reserva duplicada
            $dup = $this->db->prepare("
                SELECT id, estado FROM clases_reservas
                WHERE sesion_id = ? AND cliente_id = ?
            ");
            $dup->bind_param("ii", $sesion_id, $cliente_id);
            $dup->execute();
            $existing = $dup->get_result()->fetch_assoc();
            $dup->close();

            if ($existing) {
                if ($existing['estado'] === 'cancelada') {
                    // Reactivar reserva cancelada
                    if ($sesion['cupo_disponible'] > 0) {
                        $upd = $this->db->prepare("
                            UPDATE clases_reservas
                            SET estado = 'confirmada', cancelled_at = NULL
                            WHERE id = ?
                        ");
                        $upd->bind_param("i", $existing['id']);
                        $upd->execute();
                        $upd->close();

                        $this->decrementarCupo($sesion_id);
                        $this->db->commit();
                        return ['ok' => true, 'msg' => '¡Reserva reactivada con éxito!'];
                    } else {
                        $this->db->rollback();
                        return [
                            'ok' => false,
                            'msg' => 'La clase está llena.',
                            'lista_espera' => true
                        ];
                    }
                } else {
                    $this->db->rollback();
                    return ['ok' => false, 'msg' => 'Ya tienes una reserva activa en esta clase.'];
                }
            }

            // 3) Sin cupo → ofrecer lista de espera
            if ($sesion['cupo_disponible'] <= 0) {
                $this->db->rollback();
                return [
                    'ok' => false,
                    'msg' => 'La clase está llena. ¿Deseas entrar en la lista de espera?',
                    'lista_espera' => true
                ];
            }

            // 4) Insertar reserva
            $ins = $this->db->prepare("
                INSERT INTO clases_reservas (sesion_id, cliente_id, estado)
                VALUES (?, ?, 'confirmada')
            ");
            $ins->bind_param("ii", $sesion_id, $cliente_id);
            $ins->execute();
            $ins->close();

            // 5) Decrementar cupo
            $this->decrementarCupo($sesion_id);

            $this->db->commit();
            return ['ok' => true, 'msg' => '¡Reserva confirmada! Te esperamos en la clase.'];

        } catch (\Exception $e) {
            $this->db->rollback();
            return ['ok' => false, 'msg' => 'Error interno. Intenta de nuevo.'];
        }
    }

    /**
     * Entrar a LISTA DE ESPERA
     */
    public function entrarListaEspera(int $sesion_id, int $cliente_id): array
    {
        $this->db->begin_transaction();

        try {

            // 🔒 Bloquear la sesión para evitar condiciones de carrera
            $lock = $this->db->prepare("
            SELECT id FROM clases_sesiones
            WHERE id = ? FOR UPDATE
        ");
            $lock->bind_param("i", $sesion_id);
            $lock->execute();
            $lock->close();

            // Verificar que no tenga ya reserva activa
            $check = $this->db->prepare("
            SELECT id, estado FROM clases_reservas
            WHERE sesion_id = ? AND cliente_id = ?
        ");
            $check->bind_param("ii", $sesion_id, $cliente_id);
            $check->execute();
            $existing = $check->get_result()->fetch_assoc();
            $check->close();

            if ($existing) {
                if ($existing['estado'] === 'en_espera') {
                    $this->db->rollback();
                    return ['ok' => false, 'msg' => 'Ya estás en lista de espera.'];
                }
                if ($existing['estado'] === 'confirmada') {
                    $this->db->rollback();
                    return ['ok' => false, 'msg' => 'Ya tienes reserva confirmada.'];
                }
            }

            // Obtener siguiente posición correctamente bloqueada
            $st = $this->db->prepare("
            SELECT COALESCE(MAX(posicion_espera), 0) + 1 AS siguiente
            FROM clases_reservas
            WHERE sesion_id = ? AND estado = 'en_espera'
        ");
            $st->bind_param("i", $sesion_id);
            $st->execute();
            $pos = $st->get_result()->fetch_assoc()['siguiente'];
            $st->close();

            // Insertar
            $ins = $this->db->prepare("
            INSERT INTO clases_reservas (sesion_id, cliente_id, estado, posicion_espera)
            VALUES (?, ?, 'en_espera', ?)
        ");
            $ins->bind_param("iii", $sesion_id, $cliente_id, $pos);
            $ins->execute();
            $ins->close();

            $this->db->commit();
            return [
                'ok' => true,
                'msg' => "Estás en la lista de espera (posición #$pos)."
            ];

        } catch (\Exception $e) {
            $this->db->rollback();
            return ['ok' => false, 'msg' => 'Error al entrar en lista de espera.'];
        }
    }

    /**
     * CANCELAR reserva (libera cupo y promueve lista de espera)
     */
    public function cancelar(int $reserva_id, int $cliente_id): array
    {
        $this->db->begin_transaction();

        try {
            $st = $this->db->prepare("
                SELECT r.id, r.sesion_id, r.estado,
                       s.fecha, s.hora_inicio
                FROM clases_reservas r
                JOIN clases_sesiones s ON s.id = r.sesion_id
                WHERE r.id = ? AND r.cliente_id = ?
            ");
            $st->bind_param("ii", $reserva_id, $cliente_id);
            $st->execute();
            $reserva = $st->get_result()->fetch_assoc();
            $st->close();

            if (!$reserva) {
                $this->db->rollback();
                return ['ok' => false, 'msg' => 'Reserva no encontrada.'];
            }

            if ($reserva['estado'] === 'cancelada') {
                $this->db->rollback();
                return ['ok' => false, 'msg' => 'Esta reserva ya fue cancelada.'];
            }

            // Regla: mínimo 2 horas antes
            $claseDt = $reserva['fecha'] . ' ' . $reserva['hora_inicio'];
            $diff = strtotime($claseDt) - time();

            // ❌ Clase ya pasó
            if ($diff <= 0) {
                $this->db->rollback();
                return [
                    'ok' => false,
                    'msg' => 'No puedes cancelar una clase que ya inició o finalizó.'
                ];
            }

            // ❌ Menos de 2 horas
            if ($diff < 7200) {
                $this->db->rollback();
                return [
                    'ok' => false,
                    'msg' => 'No puedes cancelar con menos de 2 horas de anticipación.'
                ];
            }
            if ($diff < 7200 && $diff > 0) {
                $this->db->rollback();
                return [
                    'ok' => false,
                    'msg' => 'No puedes cancelar con menos de 2 horas de anticipación.'
                ];
            }

            $eraConfirmada = ($reserva['estado'] === 'confirmada');

            // Cancelar
            $upd = $this->db->prepare("
                UPDATE clases_reservas
                SET estado = 'cancelada', cancelled_at = NOW()
                WHERE id = ?
            ");
            $upd->bind_param("i", $reserva_id);
            $upd->execute();
            $upd->close();

            // Si era confirmada, liberar cupo y promover espera
            if ($eraConfirmada) {
                $this->incrementarCupo($reserva['sesion_id']);
                $this->promoverListaEspera($reserva['sesion_id']);
            }

            $this->db->commit();
            return ['ok' => true, 'msg' => 'Reserva cancelada exitosamente.'];

        } catch (\Exception $e) {
            $this->db->rollback();
            return ['ok' => false, 'msg' => 'Error al cancelar. Intenta de nuevo.'];
        }
    }

    // =========================================================
    // HELPERS PRIVADOS
    // =========================================================

    private function decrementarCupo(int $sesion_id): void
    {
        $st = $this->db->prepare("
            UPDATE clases_sesiones
            SET cupo_disponible = GREATEST(cupo_disponible - 1, 0)
            WHERE id = ?
        ");
        $st->bind_param("i", $sesion_id);
        $st->execute();
        $st->close();
    }

    private function incrementarCupo(int $sesion_id): void
    {
        $st = $this->db->prepare("
            UPDATE clases_sesiones
            SET cupo_disponible = LEAST(cupo_disponible + 1, cupo_maximo)
            WHERE id = ?
        ");
        $st->bind_param("i", $sesion_id);
        $st->execute();
        $st->close();
    }

    private function promoverListaEspera(int $sesion_id): void
    {
        $st = $this->db->prepare("
            SELECT id, cliente_id
            FROM clases_reservas
            WHERE sesion_id = ? AND estado = 'en_espera'
            ORDER BY posicion_espera ASC
            LIMIT 1
        ");
        $st->bind_param("i", $sesion_id);
        $st->execute();
        $esperando = $st->get_result()->fetch_assoc();
        $st->close();

        if ($esperando) {
            $upd = $this->db->prepare("
                UPDATE clases_reservas
                SET estado = 'confirmada', posicion_espera = NULL
                WHERE id = ?
            ");
            $upd->bind_param("i", $esperando['id']);
            $upd->execute();
            $upd->close();

            $this->decrementarCupo($sesion_id);
            $this->notificarPromocion($esperando['cliente_id'], $sesion_id);
        }
    }

    private function notificarPromocion(int $cliente_id, int $sesion_id): void
    {
        if (!defined('N8N_BASE') || !defined('N8N_TOKEN'))
            return;

        $payload = json_encode([
            'evento' => 'reserva_promovida',
            'cliente_id' => $cliente_id,
            'sesion_id' => $sesion_id,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        $url = N8N_BASE . '/webhook/reserva-promovida';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . N8N_TOKEN,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
        ]);
        @curl_exec($ch);
        curl_close($ch);
    }
}
