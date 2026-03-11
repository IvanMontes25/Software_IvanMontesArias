<?php

require_once __DIR__ . '/config/config.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error)
  die('Error de conexión');
$db->set_charset('utf8mb4');

function e($s)
{
  return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8');
}

// ── Cargar config ──
$cfg = [];
$r = $db->query("SELECT clave, valor FROM portal_config");
if ($r)
  while ($row = $r->fetch_assoc())
    $cfg[$row['clave']] = $row['valor'];

// Helper para obtener config
function c($key, $default = '')
{
  global $cfg;
  return $cfg[$key] ?? $default;
}

// ── Cargar datos ──
$features = $db->query("SELECT * FROM portal_features WHERE activo=1 ORDER BY orden ASC");
$planes = $db->query("SELECT * FROM portal_planes WHERE activo=1 ORDER BY orden ASC");
$horarios = $db->query("SELECT * FROM portal_horarios WHERE activo=1 ORDER BY dia_orden, hora_inicio ASC");
$instructores = $db->query("SELECT * FROM portal_instructores WHERE activo=1 ORDER BY orden ASC");

// Beneficios por plan
$bensByPlan = [];
$rb = $db->query("SELECT b.plan_id, b.texto FROM portal_plan_beneficios b
                   INNER JOIN portal_planes p ON p.id = b.plan_id AND p.activo=1
                   ORDER BY b.orden");
if ($rb)
  while ($b = $rb->fetch_assoc())
    $bensByPlan[(int) $b['plan_id']][] = $b['texto'];

// Agrupar horarios por día
$horariosByDia = [];
$diasOrden = [];
if ($horarios) {
  while ($h = $horarios->fetch_assoc()) {
    $horariosByDia[$h['dia']][] = $h;
    $diasOrden[$h['dia']] = $h['dia_orden'];
  }
}
uksort($horariosByDia, function ($a, $b) use ($diasOrden) {
  return ($diasOrden[$a] ?? 99) - ($diasOrden[$b] ?? 99);
});

// ── Procesar formulario de contacto ──
$contactMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['portal_contacto'])) {
  $nombre = trim($_POST['nombre'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $telefono = trim($_POST['telefono'] ?? '');
  $mensaje = trim($_POST['mensaje'] ?? '');

  if ($nombre && $email) {
    $st = $db->prepare("INSERT INTO portal_mensajes (nombre, email, telefono, mensaje) VALUES (?,?,?,?)");
    $st->bind_param('ssss', $nombre, $email, $telefono, $mensaje);
    $st->execute();
    $st->close();
    $contactMsg = 'ok';
  } else {
    $contactMsg = 'err';
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gym Body Training | La Paz, Bolivia</title>
  <link rel="icon" href="images/iconobt.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800;900&family=Bebas+Neue&display=swap"
    rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #4e73df;
      --primary-dark: #224abe;
      --success: #1cc88a;
      --warning: #f6c23e;
      --danger: #e74a3b;
      --orange: #ff6b35;
      --dark: #0b1221;
      --darker: #060d18;
      --glass-bg: rgba(255, 255, 255, .06);
      --glass-stroke: rgba(255, 255, 255, .12);
      --glass-hover: rgba(255, 255, 255, .10);
      --soft-shadow: 0 8px 32px rgba(0, 0, 0, .3);
      --ring: rgba(78, 115, 223, .35);
      --gradient-main: linear-gradient(135deg, #4e73df, #1cc88a);
      --gradient-warm: linear-gradient(135deg, #ff6b35, #f6c23e);
      --radius: 1.25rem;
      --radius-sm: .75rem
    }

    *,
    *::before,
    *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box
    }

    html {
      scroll-behavior: smooth;
      scroll-padding-top: 80px
    }

    body {
      font-family: 'Nunito', system-ui, sans-serif;
      background: var(--darker);
      color: #e2e8f0;
      overflow-x: hidden;
      line-height: 1.6
    }

    img {
      max-width: 100%;
      display: block
    }

    a {
      text-decoration: none;
      color: inherit
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 24px
    }

    .section-pad {
      padding: 100px 0
    }

    .section-title {
      font-family: 'Bebas Neue', 'Nunito', sans-serif;
      font-size: clamp(2.5rem, 6vw, 4rem);
      letter-spacing: 2px;
      text-align: center;
      margin-bottom: 12px;
      background: var(--gradient-main);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text
    }

    .section-sub {
      text-align: center;
      color: rgba(255, 255, 255, .6);
      font-size: 1.05rem;
      max-width: 600px;
      margin: 0 auto 60px;
      font-weight: 300
    }

    .gradient-text {
      background: var(--gradient-main);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text
    }

    .btn-main {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 36px;
      border-radius: 50px;
      border: none;
      background: var(--gradient-main);
      color: #fff;
      font-family: 'Nunito', sans-serif;
      font-weight: 800;
      font-size: 1rem;
      cursor: pointer;
      transition: .3s;
      box-shadow: 0 4px 20px rgba(78, 115, 223, .4);
      text-transform: uppercase;
      letter-spacing: 1px
    }

    .btn-main:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(78, 115, 223, .5);
      color: #fff
    }

    .btn-outline {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 36px;
      border-radius: 50px;
      border: 2px solid rgba(255, 255, 255, .3);
      background: transparent;
      color: #fff;
      font-family: 'Nunito', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: .3s;
      text-transform: uppercase;
      letter-spacing: 1px
    }

    .btn-outline:hover {
      border-color: var(--success);
      color: var(--success);
      transform: translateY(-3px)
    }

    [data-reveal] {
      opacity: 0;
      transform: translateY(40px);
      transition: opacity .8s cubic-bezier(.25, .46, .45, .94), transform .8s cubic-bezier(.25, .46, .45, .94)
    }

    [data-reveal].visible {
      opacity: 1;
      transform: translateY(0)
    }

    [data-reveal-left] {
      opacity: 0;
      transform: translateX(-60px);
      transition: opacity .8s ease, transform .8s ease
    }

    [data-reveal-left].visible {
      opacity: 1;
      transform: translateX(0)
    }

    [data-reveal-right] {
      opacity: 0;
      transform: translateX(60px);
      transition: opacity .8s ease, transform .8s ease
    }

    [data-reveal-right].visible {
      opacity: 1;
      transform: translateX(0)
    }

    .particles-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 0;
      overflow: hidden
    }

    .bubble {
      position: absolute;
      bottom: -80px;
      border-radius: 50%;
      background: rgba(78, 115, 223, .08);
      animation: rise linear infinite
    }

    @keyframes rise {
      from {
        transform: translateY(0) rotate(0);
        opacity: .4
      }

      to {
        transform: translateY(-120vh) rotate(360deg);
        opacity: 0
      }
    }

    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      padding: 16px 0;
      transition: .4s
    }

    .navbar.scrolled {
      background: rgba(11, 18, 33, .92);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, .05);
      padding: 10px 0;
      box-shadow: 0 4px 30px rgba(0, 0, 0, .3)
    }

    .nav-inner {
      display: flex;
      align-items: center;
      justify-content: space-between
    }

    .nav-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 900;
      font-size: 1.2rem;
      color: #fff
    }

    .nav-brand-icon {
      width: 42px;
      height: 42px;
      background: var(--gradient-warm);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: #fff;
      box-shadow: 0 4px 15px rgba(255, 107, 53, .4)
    }

    .nav-links {
      display: flex;
      gap: 32px;
      list-style: none;
      align-items: center
    }

    .nav-links a {
      color: rgba(255, 255, 255, .7);
      font-weight: 600;
      font-size: .95rem;
      transition: color .3s;
      position: relative
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -4px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--gradient-main);
      transition: width .3s;
      border-radius: 2px
    }

    .nav-links a:hover {
      color: #fff
    }

    .nav-links a:hover::after {
      width: 100%
    }

    .nav-cta {
      padding: 10px 24px !important;
      border-radius: 50px !important;
      background: var(--gradient-main) !important;
      color: #fff !important;
      font-weight: 700 !important;
      box-shadow: 0 4px 15px rgba(78, 115, 223, .3);
      transition: transform .3s, box-shadow .3s !important
    }

    .nav-cta::after {
      display: none !important
    }

    .nav-cta:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(78, 115, 223, .5)
    }

    .hamburger {
      display: none;
      flex-direction: column;
      gap: 5px;
      cursor: pointer;
      background: none;
      border: none;
      padding: 8px
    }

    .hamburger span {
      width: 28px;
      height: 3px;
      background: #fff;
      border-radius: 3px;
      transition: .3s
    }

    .hamburger.active span:nth-child(1) {
      transform: rotate(45deg) translate(5px, 6px)
    }

    .hamburger.active span:nth-child(2) {
      opacity: 0
    }

    .hamburger.active span:nth-child(3) {
      transform: rotate(-45deg) translate(5px, -6px)
    }

    .hero {
      position: relative;
      min-height: 100vh;
      display: flex;
      align-items: center;
      overflow: hidden
    }

    .hero-bg {
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(11, 18, 33, .85) 0%, rgba(34, 74, 190, .5) 50%, rgba(28, 200, 138, .3) 100%), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1920&q=80') center/cover no-repeat;
      z-index: 0
    }

    .hero-bg::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 200px;
      background: linear-gradient(to top, var(--darker), transparent)
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 700px
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 20px;
      border-radius: 50px;
      background: rgba(255, 255, 255, .08);
      border: 1px solid rgba(255, 255, 255, .15);
      backdrop-filter: blur(8px);
      font-size: .85rem;
      font-weight: 600;
      color: var(--warning);
      margin-bottom: 24px;
      animation: fadeInDown 1s ease .2s both
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-20px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    .hero h1 {
      font-family: 'Bebas Neue', 'Nunito', sans-serif;
      font-size: clamp(3.5rem, 9vw, 6.5rem);
      line-height: .95;
      letter-spacing: 3px;
      margin-bottom: 24px;
      animation: fadeInUp 1s ease .4s both
    }

    .hero h1 .accent {
      background: var(--gradient-warm);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    .hero p {
      font-size: 1.15rem;
      color: rgba(255, 255, 255, .75);
      max-width: 520px;
      margin-bottom: 36px;
      font-weight: 300;
      animation: fadeInUp 1s ease .6s both
    }

    .hero-btns {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
      animation: fadeInUp 1s ease .8s both
    }

    .hero-stats {
      position: relative;
      z-index: 2;
      display: flex;
      gap: 48px;
      margin-top: 80px;
      animation: fadeInUp 1s ease 1s both
    }

    .hero-stat h3 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 3rem;
      line-height: 1;
      background: var(--gradient-main);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text
    }

    .hero-stat span {
      font-size: .85rem;
      color: rgba(255, 255, 255, .5);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px
    }

    .hero-deco {
      position: absolute;
      z-index: 1;
      border-radius: 50%;
      filter: blur(60px);
      opacity: .3
    }

    .hero-deco-1 {
      width: 400px;
      height: 400px;
      background: var(--primary);
      top: 10%;
      right: -5%;
      animation: float 8s ease-in-out infinite
    }

    .hero-deco-2 {
      width: 300px;
      height: 300px;
      background: var(--success);
      bottom: 15%;
      right: 15%;
      animation: float 10s ease-in-out infinite reverse
    }

    @keyframes float {

      0%,
      100% {
        transform: translate(0, 0)
      }

      50% {
        transform: translate(-30px, -40px)
      }
    }

    .about {
      background: var(--darker);
      position: relative;
      z-index: 1
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px
    }

    .feature-card {
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      border-radius: var(--radius);
      padding: 36px 28px;
      transition: .4s;
      position: relative;
      overflow: hidden
    }

    .feature-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--gradient-main);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform .4s
    }

    .feature-card:hover::before {
      transform: scaleX(1)
    }

    .feature-card:hover {
      transform: translateY(-8px);
      border-color: rgba(78, 115, 223, .3);
      box-shadow: 0 20px 50px rgba(0, 0, 0, .3);
      background: var(--glass-hover)
    }

    .feature-icon {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #fff
    }

    .feature-card h3 {
      font-size: 1.2rem;
      font-weight: 800;
      margin-bottom: 10px;
      color: #fff
    }

    .feature-card p {
      color: rgba(255, 255, 255, .55);
      font-size: .95rem;
      font-weight: 300
    }

    .memberships {
      background: linear-gradient(180deg, var(--darker) 0%, #0d1a30 100%);
      position: relative;
      z-index: 1
    }

    .plans-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 28px;
      max-width: 1000px;
      margin: 0 auto
    }

    .plan-card {
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      border-radius: var(--radius);
      padding: 40px 32px;
      text-align: center;
      transition: .4s;
      position: relative;
      overflow: hidden
    }

    .plan-card.featured {
      border-color: rgba(78, 115, 223, .4);
      background: rgba(78, 115, 223, .08);
      transform: scale(1.03)
    }

    .plan-card.featured::before {
      content: 'MÁS POPULAR';
      position: absolute;
      top: 20px;
      right: -35px;
      background: var(--gradient-warm);
      color: #fff;
      font-size: .7rem;
      font-weight: 800;
      padding: 6px 40px;
      transform: rotate(45deg);
      letter-spacing: 1px
    }

    .plan-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(0, 0, 0, .3)
    }

    .plan-card.featured:hover {
      transform: scale(1.03) translateY(-8px)
    }

    .plan-icon {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin: 0 auto 20px;
      color: #fff
    }

    .plan-name {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.8rem;
      letter-spacing: 2px;
      margin-bottom: 8px;
      color: #fff
    }

    .plan-price {
      font-size: 2.8rem;
      font-weight: 900;
      margin-bottom: 4px
    }

    .plan-price small {
      font-size: .9rem;
      font-weight: 400;
      color: rgba(255, 255, 255, .5)
    }

    .plan-duration {
      color: rgba(255, 255, 255, .5);
      font-size: .9rem;
      margin-bottom: 24px
    }

    .plan-features {
      list-style: none;
      text-align: left;
      margin-bottom: 32px
    }

    .plan-features li {
      padding: 10px 0;
      border-bottom: 1px solid rgba(255, 255, 255, .06);
      color: rgba(255, 255, 255, .7);
      font-size: .95rem;
      display: flex;
      align-items: center;
      gap: 10px
    }

    .plan-features li i {
      color: var(--success);
      font-size: .8rem
    }

    .schedule {
      background: var(--darker);
      position: relative;
      z-index: 1
    }

    .schedule-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px
    }

    .day-card {
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      border-radius: var(--radius);
      padding: 24px 16px;
      text-align: center;
      transition: .3s
    }

    .day-card:hover {
      border-color: rgba(78, 115, 223, .4);
      transform: translateY(-4px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, .2)
    }

    .day-name {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.4rem;
      letter-spacing: 2px;
      margin-bottom: 16px;
      color: #fff
    }

    .class-item {
      padding: 10px 0;
      border-bottom: 1px solid rgba(255, 255, 255, .05)
    }

    .class-time {
      font-size: .75rem;
      color: var(--warning);
      font-weight: 700
    }

    .class-name {
      font-size: .85rem;
      color: rgba(255, 255, 255, .7);
      font-weight: 600
    }

    .instructors {
      background: linear-gradient(180deg, var(--darker) 0%, #0d1a30 100%);
      position: relative;
      z-index: 1
    }

    .instructors-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 28px
    }

    .instructor-card {
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      border-radius: var(--radius);
      overflow: hidden;
      transition: .4s
    }

    .instructor-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(0, 0, 0, .3);
      border-color: rgba(28, 200, 138, .3)
    }

    .instructor-avatar {
      height: 220px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 4rem;
      position: relative;
      overflow: hidden
    }

    .instructor-avatar::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 80px;
      background: linear-gradient(to top, rgba(11, 18, 33, 1), transparent)
    }

    .instructor-info {
      padding: 20px 24px 28px
    }

    .instructor-info h3 {
      font-size: 1.15rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: 4px
    }

    .instructor-info .role {
      color: var(--success);
      font-size: .85rem;
      font-weight: 700;
      margin-bottom: 12px
    }

    .instructor-info p {
      color: rgba(255, 255, 255, .5);
      font-size: .9rem;
      font-weight: 300
    }

    .location {
      background: var(--darker);
      position: relative;
      z-index: 1
    }

    .location-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      align-items: center
    }

    .map-container {
      border-radius: var(--radius);
      overflow: hidden;
      border: 1px solid var(--glass-stroke);
      height: 400px;
      box-shadow: var(--soft-shadow)
    }

    .map-container iframe {
      width: 100%;
      height: 100%;
      border: 0;
      filter: brightness(.8) contrast(1.1) saturate(.8)
    }

    .location-info {
      display: flex;
      flex-direction: column;
      gap: 28px
    }

    .loc-item {
      display: flex;
      gap: 16px;
      align-items: flex-start
    }

    .loc-icon {
      width: 50px;
      height: 50px;
      min-width: 50px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: #fff
    }

    .loc-item h4 {
      font-size: 1rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: 4px
    }

    .loc-item p {
      color: rgba(255, 255, 255, .55);
      font-size: .9rem;
      font-weight: 300
    }

    .contact {
      background: linear-gradient(180deg, var(--darker) 0%, #0d1a30 100%);
      position: relative;
      z-index: 1
    }

    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 48px;
      align-items: start
    }

    .contact-form {
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      border-radius: var(--radius);
      padding: 40px 32px;
      backdrop-filter: blur(12px)
    }

    .form-group {
      position: relative;
      margin-bottom: 20px
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 16px 20px 16px 48px;
      background: rgba(255, 255, 255, .05);
      border: 1px solid rgba(255, 255, 255, .1);
      border-radius: 12px;
      color: #fff;
      font-family: 'Nunito', sans-serif;
      font-size: .95rem;
      transition: .3s;
      outline: none
    }

    .form-group textarea {
      min-height: 120px;
      resize: vertical
    }

    .form-group input:focus,
    .form-group textarea:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px var(--ring);
      background: rgba(255, 255, 255, .08)
    }

    .form-group i {
      position: absolute;
      left: 18px;
      top: 18px;
      color: rgba(255, 255, 255, .4);
      font-size: .95rem
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
      color: rgba(255, 255, 255, .35)
    }

    .contact-text h3 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 2.5rem;
      letter-spacing: 2px;
      margin-bottom: 16px;
      color: #fff
    }

    .contact-text p {
      color: rgba(255, 255, 255, .55);
      font-size: 1rem;
      margin-bottom: 32px;
      font-weight: 300
    }

    .social-links {
      display: flex;
      gap: 16px;
      margin-top: 32px
    }

    .social-link {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: var(--glass-bg);
      border: 1px solid var(--glass-stroke);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: rgba(255, 255, 255, .7);
      transition: .3s
    }

    .social-link:hover {
      background: var(--primary);
      border-color: var(--primary);
      color: #fff;
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(78, 115, 223, .4)
    }

    .footer {
      background: #060a14;
      border-top: 1px solid rgba(255, 255, 255, .05);
      padding: 40px 0 24px;
      position: relative;
      z-index: 1
    }

    .footer-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px
    }

    .footer-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 800;
      font-size: 1rem;
      color: rgba(255, 255, 255, .6)
    }

    .footer-copy {
      color: rgba(255, 255, 255, .35);
      font-size: .85rem
    }

    .footer-links {
      display: flex;
      gap: 24px;
      list-style: none
    }

    .footer-links a {
      color: rgba(255, 255, 255, .4);
      font-size: .85rem;
      transition: color .3s
    }

    .footer-links a:hover {
      color: var(--success)
    }

    .glow-divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(78, 115, 223, .3), rgba(28, 200, 138, .3), transparent);
      border: none;
      margin: 0
    }

    .grain {
      position: fixed;
      inset: 0;
      z-index: 9999;
      pointer-events: none;
      opacity: .03;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
      background-repeat: repeat
    }

    .alert-portal {
      padding: 16px 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      font-weight: 600;
      text-align: center
    }

    .alert-ok {
      background: rgba(28, 200, 138, .15);
      border: 1px solid rgba(28, 200, 138, .4);
      color: #1cc88a
    }

    .alert-err {
      background: rgba(231, 74, 59, .15);
      border: 1px solid rgba(231, 74, 59, .4);
      color: #e74a3b
    }

    ::-webkit-scrollbar {
      width: 8px
    }

    ::-webkit-scrollbar-track {
      background: var(--darker)
    }

    ::-webkit-scrollbar-thumb {
      background: linear-gradient(var(--primary), var(--success));
      border-radius: 4px
    }

    @media(max-width:768px) {
      .nav-links {
        display: none
      }

      .hamburger {
        display: flex
      }

      .nav-links.open {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: rgba(11, 18, 33, .97);
        backdrop-filter: blur(20px);
        padding: 24px;
        gap: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, .05)
      }

      .hero h1 {
        font-size: clamp(2.8rem, 10vw, 4rem)
      }

      .hero-stats {
        gap: 24px;
        flex-wrap: wrap
      }

      .hero-btns {
        flex-direction: column
      }

      .hero-btns .btn-main,
      .hero-btns .btn-outline {
        width: 100%;
        justify-content: center
      }

      .location-grid,
      .contact-grid {
        grid-template-columns: 1fr
      }

      .plans-grid {
        grid-template-columns: 1fr
      }

      .plan-card.featured {
        transform: none
      }

      .plan-card.featured:hover {
        transform: translateY(-8px)
      }

      .section-pad {
        padding: 70px 0
      }
    }
  </style>
</head>

<body>
  <div class="grain"></div>
  <div class="particles-container" id="particles"></div>

  <!-- NAVBAR -->
  <nav class="navbar" id="navbar">
    <div class="container nav-inner">
      <a href="#" class="nav-brand">
        <div class="nav-brand-icon"><i class="fas fa-dumbbell"></i></div>
        <span>BODY <span class="gradient-text">TRAINING</span></span>
      </a>
      <ul class="nav-links" id="navLinks">
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#nosotros">Nosotros</a></li>
        <li><a href="#membresias">Membresías</a></li>
        <li><a href="#horarios">Horarios</a></li>
        <li><a href="#instructores">Equipo</a></li>
        <li><a href="#ubicacion">Ubicación</a></li>
        <li><a href="#contacto" class="nav-cta">Únete</a></li>
      </ul>
      <button class="hamburger" id="hamburger" aria-label="Menú"><span></span><span></span><span></span></button>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero" id="inicio">
    <div class="hero-bg"></div>
    <div class="hero-deco hero-deco-1"></div>
    <div class="hero-deco hero-deco-2"></div>
    <div class="container" style="position:relative;z-index:2;">
      <div class="hero-content">
        <div class="hero-badge"><i class="fas fa-bolt"></i> <?= e(c('hero_badge')) ?></div>
        <h1>
          <?= e(c('hero_titulo_1', 'TRANSFORMA')) ?><br>
          <?= e(c('hero_titulo_2', 'TU CUERPO')) ?> <span class="accent"><?= '' ?></span><br>
          <?= e(c('hero_titulo_3', 'TU VIDA')) ?>
        </h1>
        <p><?= e(c('hero_descripcion')) ?></p>
        <div class="hero-btns">
          <a href="#membresias" class="btn-main"><i class="fas fa-fire"></i> Ver Planes</a>
          <a href="#contacto" class="btn-outline"><i class="fas fa-calendar-check"></i> Clase de prueba</a>
        </div>
      </div>
      <div class="hero-stats">
        <?php for ($i = 1; $i <= 4; $i++): ?>
          <?php $num = c("hero_stat_{$i}_num", '0');
          $lbl = c("hero_stat_{$i}_label", '');
          if (!$lbl)
            continue; ?>
          <div class="hero-stat">
            <h3><span class="counter" data-target="<?= e($num) ?>">0</span><?= $i <= 2 ? '+' : '' ?></h3>
            <span><?= e($lbl) ?></span>
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </section>
  <hr class="glow-divider">

  <!-- FEATURES -->
  <section class="about section-pad" id="nosotros">
    <div class="container">
      <h2 class="section-title" data-reveal>¿POR QUÉ BODY TRAINING?</h2>
      <p class="section-sub" data-reveal>No somos solo un gimnasio. Somos tu segundo hogar donde cada gota de sudor
        cuenta.</p>
      <div class="features-grid">
        <?php if ($features) {
          $features->data_seek(0);
          while ($f = $features->fetch_assoc()): ?>
            <div class="feature-card" data-reveal>
              <div class="feature-icon" style="background: <?= e($f['color']) ?>;">
                <i class="<?= e($f['icono']) ?>"></i>
              </div>
              <h3><?= e($f['titulo']) ?></h3>
              <p><?= e($f['descripcion']) ?></p>
            </div>
          <?php endwhile;
        } ?>
      </div>
    </div>
  </section>
  <hr class="glow-divider">

  <!-- PLANES -->
  <section class="memberships section-pad" id="membresias">
    <div class="container">
      <h2 class="section-title" data-reveal>PLANES DE MEMBRESÍA</h2>
      <p class="section-sub" data-reveal>Elige el plan que se adapte a tu estilo de vida.</p>
      <div class="plans-grid">
        <?php if ($planes) {
          $planes->data_seek(0);
          while ($p = $planes->fetch_assoc()): ?>
            <div class="plan-card <?= $p['destacado'] ? 'featured' : '' ?>" data-reveal>
              <div class="plan-icon" style="background: <?= e($p['color']) ?>;"><i class="<?= e($p['icono']) ?>"></i></div>
              <div class="plan-name"><?= e($p['nombre']) ?></div>
              <div class="plan-price gradient-text"><?= number_format($p['precio'], 0) ?>
                <small><?= e($p['moneda']) ?>/mes</small></div>
              <div class="plan-duration"><?= e($p['duracion']) ?> · <?= e($p['tipo_acceso']) ?></div>
              <ul class="plan-features">
                <?php foreach (($bensByPlan[(int) $p['id']] ?? []) as $ben): ?>
                  <li><i class="fas fa-check-circle"></i> <?= e($ben) ?></li>
                <?php endforeach; ?>
              </ul>
              <a href="#contacto" class="<?= $p['destacado'] ? 'btn-main' : 'btn-outline' ?>"
                style="width:100%;justify-content:center;">Elegir plan</a>
            </div>
          <?php endwhile;
        } ?>
      </div>
    </div>
  </section>
  <hr class="glow-divider">

  <!-- HORARIOS -->
  <section class="schedule section-pad" id="horarios">
    <div class="container">
      <h2 class="section-title" data-reveal>HORARIOS DE CLASES</h2>
      <p class="section-sub" data-reveal>Clases todos los días. Encuentra el horario perfecto para tu rutina.</p>
      <div class="schedule-grid" data-reveal>
        <?php foreach ($horariosByDia as $dia => $clases): ?>
          <div class="day-card">
            <div class="day-name"><?= e($dia) ?></div>
            <?php foreach ($clases as $cl): ?>
              <div class="class-item">
                <div class="class-time"><?= e($cl['hora_inicio']) ?> - <?= e($cl['hora_fin']) ?></div>
                <div class="class-name"><?= e($cl['clase']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="text-align:center;margin-top:40px;color:rgba(255,255,255,.4);font-size:.9rem;" data-reveal>
        <i class="fas fa-clock"></i> Horario general: <strong
          style="color:var(--success);"><?= e(c('horario_ls')) ?></strong> · <strong
          style="color:var(--warning);"><?= e(c('horario_dom')) ?></strong>
      </div>
    </div>
  </section>
  <hr class="glow-divider">

  <!-- INSTRUCTORES -->
  <section class="instructors section-pad" id="instructores">
    <div class="container">
      <h2 class="section-title" data-reveal>NUESTRO EQUIPO</h2>
      <p class="section-sub" data-reveal>Profesionales apasionados que te acompañarán en cada paso.</p>
      <div class="instructors-grid">
        <?php if ($instructores) {
          $instructores->data_seek(0);
          while ($inst = $instructores->fetch_assoc()): ?>
            <div class="instructor-card" data-reveal>
              <div class="instructor-avatar" style="background: <?= e($inst['color']) ?>;">
                <i class="<?= e($inst['icono']) ?>" style="color: var(--primary);"></i>
              </div>
              <div class="instructor-info">
                <h3><?= e($inst['nombre']) ?></h3>
                <div class="role"><?= e($inst['cargo']) ?></div>
                <p><?= e($inst['descripcion']) ?></p>
              </div>
            </div>
          <?php endwhile;
        } ?>
      </div>
    </div>
  </section>
  <hr class="glow-divider">

  <!-- UBICACIÓN -->
  <section class="location section-pad" id="ubicacion">
    <div class="container">
      <h2 class="section-title" data-reveal>ENCUÉNTRANOS</h2>
      <p class="section-sub" data-reveal>Ubicados en el corazón de La Paz, de fácil acceso.</p>
      <div class="location-grid">
        <div class="map-container" data-reveal-left>
          <iframe src="<?= e(c('mapa_embed')) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="location-info" data-reveal-right>
          <div class="loc-item">
            <div class="loc-icon" style="background:var(--gradient-main);"><i class="fas fa-map-marker-alt"></i></div>
            <div>
              <h4>Dirección</h4>
              <p><?= nl2br(e(c('direccion'))) ?></p>
            </div>
          </div>
          <div class="loc-item">
            <div class="loc-icon" style="background:var(--gradient-warm);"><i class="fas fa-clock"></i></div>
            <div>
              <h4>Horario</h4>
              <p><?= e(c('horario_ls')) ?><br><?= e(c('horario_dom')) ?></p>
            </div>
          </div>
          <div class="loc-item">
            <div class="loc-icon" style="background:linear-gradient(135deg,#1cc88a,#17a673);"><i
                class="fas fa-phone-alt"></i></div>
            <div>
              <h4>Teléfono</h4>
              <p><?= e(c('telefono_1')) ?><br><?= e(c('telefono_2')) ?></p>
            </div>
          </div>
          <div class="loc-item">
            <div class="loc-icon" style="background:linear-gradient(135deg,#6f42c1,#5a32a3);"><i
                class="fas fa-envelope"></i></div>
            <div>
              <h4>Email</h4>
              <p><?= e(c('email_1')) ?><br><?= e(c('email_2')) ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <hr class="glow-divider">

  <!-- CONTACTO -->
  <section class="contact section-pad" id="contacto">
    <div class="container">
      <h2 class="section-title" data-reveal>CONTÁCTANOS</h2>
      <p class="section-sub" data-reveal>¿Listo para empezar? Escríbenos y te ayudaremos.</p>
      <div class="contact-grid">
        <div class="contact-text" data-reveal-left>
          <h3>EMPIEZA TU<br>TRANSFORMACIÓN<br><span class="gradient-text">HOY MISMO</span></h3>
          <p>Rellena el formulario y nos pondremos en contacto contigo. ¡La primera clase es gratis!</p>
          <div style="display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;gap:12px;"><i class="fab fa-whatsapp"
                style="font-size:1.5rem;color:var(--success);"></i><span><?= e(c('telefono_2')) ?></span></div>
            <div style="display:flex;align-items:center;gap:12px;"><i class="fas fa-envelope"
                style="font-size:1.2rem;color:var(--primary);"></i><span><?= e(c('email_1')) ?></span></div>
          </div>
          <div class="social-links">
            <?php if (c('social_facebook', '#') !== '#' || true): ?>
              <a href="<?= e(c('social_facebook', '#')) ?>" class="social-link" target="_blank"><i
                  class="fab fa-facebook-f"></i></a>
            <?php endif; ?>
            <a href="<?= e(c('social_instagram', '#')) ?>" class="social-link" target="_blank"><i
                class="fab fa-instagram"></i></a>
            <a href="<?= e(c('social_tiktok', '#')) ?>" class="social-link" target="_blank"><i
                class="fab fa-tiktok"></i></a>
            <a href="<?= e(c('social_whatsapp', '#')) ?>" class="social-link" target="_blank"><i
                class="fab fa-whatsapp"></i></a>
          </div>
        </div>
        <div class="contact-form" data-reveal-right>
          <?php if ($contactMsg === 'ok'): ?>
            <div class="alert-portal alert-ok"><i class="fas fa-check-circle mr-1"></i> ¡Mensaje enviado! Nos pondremos en
              contacto pronto.</div>
          <?php elseif ($contactMsg === 'err'): ?>
            <div class="alert-portal alert-err"><i class="fas fa-exclamation-circle mr-1"></i> Por favor completa nombre y
              email.</div>
          <?php endif; ?>
          <form method="POST" action="portal.php#contacto">
            <input type="hidden" name="portal_contacto" value="1">
            <div class="form-group"><i class="fas fa-user"></i><input type="text" name="nombre"
                placeholder="Nombre completo" required></div>
            <div class="form-group"><i class="fas fa-envelope"></i><input type="email" name="email"
                placeholder="Correo electrónico" required></div>
            <div class="form-group"><i class="fas fa-phone"></i><input type="tel" name="telefono"
                placeholder="Teléfono / WhatsApp"></div>
            <div class="form-group"><i class="fas fa-comment-dots"></i><textarea name="mensaje"
                placeholder="¿En qué podemos ayudarte?"></textarea></div>
            <button type="submit" class="btn-main" style="width:100%;justify-content:center;"><i
                class="fas fa-paper-plane"></i> Enviar mensaje</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="footer-inner">
        <div class="footer-brand">
          <div class="nav-brand-icon" style="width:32px;height:32px;font-size:.9rem;"><i class="fas fa-dumbbell"></i>
          </div><span>BODY TRAINING</span>
        </div>
        <ul class="footer-links">
          <li><a href="#inicio">Inicio</a></li>
          <li><a href="#membresias">Planes</a></li>
          <li><a href="#horarios">Horarios</a></li>
          <li><a href="#contacto">Contacto</a></li>
        </ul>
        <div class="footer-copy">&copy; <?= date('Y') ?> Gym Body Training — La Paz, Bolivia.</div>
      </div>
    </div>
  </footer>

  <script>
    document.getElementById('year') && (document.getElementById('year').textContent = new Date().getFullYear());
    (function () { const c = document.getElementById('particles'); for (let i = 0; i < 22; i++) { const b = document.createElement('div'); b.className = 'bubble'; const s = Math.random() * 14 + 6; b.style.cssText = `left:${Math.random() * 100}%;width:${s}px;height:${s}px;animation-duration:${Math.random() * 10 + 10}s;animation-delay:-${Math.random() * 15}s;opacity:${Math.random() * .3 + .1};`; c.appendChild(b) } })();
    const navbar = document.getElementById('navbar'); window.addEventListener('scroll', () => { navbar.classList.toggle('scrolled', window.scrollY > 60) });
    const hamburger = document.getElementById('hamburger'), navLinks = document.getElementById('navLinks'); hamburger.addEventListener('click', () => { hamburger.classList.toggle('active'); navLinks.classList.toggle('open') }); navLinks.querySelectorAll('a').forEach(a => { a.addEventListener('click', () => { hamburger.classList.remove('active'); navLinks.classList.remove('open') }) });
    const ro = new IntersectionObserver(e => { e.forEach(en => { if (en.isIntersecting) en.target.classList.add('visible') }) }, { threshold: .12, rootMargin: '0px 0px -40px 0px' }); document.querySelectorAll('[data-reveal],[data-reveal-left],[data-reveal-right]').forEach(el => ro.observe(el));
    const co = new IntersectionObserver(e => { e.forEach(en => { if (en.isIntersecting) { const el = en.target, t = +el.dataset.target, d = 2e3, s = performance.now(); function step(n) { const p = Math.min((n - s) / d, 1), ea = 1 - Math.pow(1 - p, 3); el.textContent = Math.floor(ea * t); if (p < 1) requestAnimationFrame(step); else el.textContent = t } requestAnimationFrame(step); co.unobserve(el) } }) }, { threshold: .5 }); document.querySelectorAll('.counter').forEach(el => co.observe(el));
  </script>
</body>

</html>