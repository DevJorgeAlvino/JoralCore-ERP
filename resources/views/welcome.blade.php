<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="JoralCore ERP - Plataforma Central de Gestión Empresarial">
    <title>JoralCore ERP</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet"/>

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css'])

    <style>
        *, html { box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Fondo grid */
        .page-bg {
            min-height: 100vh;
            min-height: 100dvh; /* dynamic viewport height - mejor en móvil */
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            background-image:
                linear-gradient(to right, rgba(0,0,0,0.04) 1px, transparent 1px),
                linear-gradient(to bottom,  rgba(0,0,0,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            position: relative;
            /* overflow visible para permitir scroll en landscape */
            overflow-x: hidden;
            overflow-y: auto;
            padding: 24px 0;
        }

        /* Orbes decorativos */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            animation: pulse-orb 8s ease-in-out infinite;
        }
        .orb-1 {
            width: 400px; height: 400px;
            top: -120px; right: -100px;
            background: rgba(251, 191, 36, 0.12);
        }
        .orb-2 {
            width: 350px; height: 350px;
            bottom: -100px; left: -80px;
            background: rgba(251, 191, 36, 0.08);
            animation-delay: 4s;
        }

        @keyframes pulse-orb {
            0%, 100% { opacity: 0.5; }
            50%       { opacity: 1;   }
        }

        /* Card */
        .card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            margin: 0 20px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(229, 231, 235, 0.7);
            border-radius: 28px;
            padding: 40px 36px;
            box-shadow:
                0 1px 2px rgba(0,0,0,0.04),
                0 8px 32px rgba(0,0,0,0.06),
                0 24px 64px rgba(0,0,0,0.04);
            animation: fade-up 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Tablet portrait y landscape */
        @media (min-width: 640px) {
            .card {
                max-width: 480px;
                padding: 56px 56px;
                border-radius: 32px;
            }
        }

        /* Desktop */
        @media (min-width: 1024px) {
            .card {
                max-width: 520px;
                padding: 64px 72px;
            }
        }

        /* ── Móvil landscape: pantalla ancha pero muy baja ── */
        @media (orientation: landscape) and (max-height: 500px) {
            .page-bg {
                align-items: flex-start;
                padding: 16px 0;
            }
            .card {
                max-width: 560px;
                padding: 20px 32px;
                border-radius: 20px;
            }
        }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        /* Logo icon */
        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }
        .logo-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 24px rgba(245, 158, 11, 0.38);
        }
        .logo-box svg {
            width: 30px;
            height: 30px;
            color: #fff;
            stroke: #fff;
            fill: none;
        }

        @media (min-width: 640px) {
            .logo-wrap { margin-bottom: 32px; }
            .logo-box { width: 72px; height: 72px; border-radius: 20px; }
            .logo-box svg { width: 36px; height: 36px; }
        }

        /* Logo compacto en landscape móvil */
        @media (orientation: landscape) and (max-height: 500px) {
            .logo-wrap { margin-bottom: 0; }
            .logo-box { width: 44px; height: 44px; border-radius: 12px; }
            .logo-box svg { width: 22px; height: 22px; }
        }

        /* Textos */
        .brand-title {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #111827;
            margin: 0 0 8px;
        }
        .brand-title span { color: #f59e0b; }

        .brand-sub {
            text-align: center;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #9ca3af;
            margin: 0 0 28px;
        }

        @media (min-width: 640px) {
            .brand-title { font-size: 32px; letter-spacing: -0.8px; margin-bottom: 10px; }
            .brand-sub   { font-size: 12px; letter-spacing: 3px; margin-bottom: 36px; }
        }

        /* Textos compactos en landscape móvil */
        @media (orientation: landscape) and (max-height: 500px) {
            .brand-title { font-size: 20px; margin-bottom: 4px; }
            .brand-sub   { font-size: 10px; letter-spacing: 2px; margin-bottom: 16px; }
        }

        /* Separador */
        .divider {
            height: 1px;
            width: 100%;
            background: linear-gradient(90deg, transparent, rgba(245, 158, 11, 0.25), transparent);
            margin-bottom: 24px;
        }

        @media (orientation: landscape) and (max-height: 500px) {
            .divider { margin-bottom: 16px; }
        }

        /* Botones */
        .btn-wrap {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 24px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.1px;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
        }
        .btn svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke: currentColor;
            fill: none;
        }

        @media (min-width: 640px) {
            .btn-wrap { gap: 14px; }
            .btn { padding: 15px 28px; font-size: 16px; border-radius: 16px; }
            .btn svg { width: 20px; height: 20px; }
        }

        /* Botones lado a lado en landscape móvil */
        @media (orientation: landscape) and (max-height: 500px) {
            .btn-wrap { flex-direction: row; gap: 10px; }
            .btn { padding: 10px 16px; font-size: 13px; border-radius: 12px; }
            .btn svg { width: 15px; height: 15px; }
        }

        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 12px rgba(245, 158, 11, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(245, 158, 11, 0.40);
        }

        .btn-secondary {
            background: #ffffff;
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        .btn-secondary:hover {
            transform: translateY(-1px);
            border-color: rgba(245, 158, 11, 0.35);
            color: #d97706;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }

        /* Footer */
        .footer-note {
            position: relative;
            z-index: 10;
            text-align: center;
            font-size: 10px;
            color: #d1d5db;
            margin-top: 16px;
            letter-spacing: 0.5px;
            animation: fade-up 0.7s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Ocultar footer en landscape muy pequeño para ganar espacio */
        @media (orientation: landscape) and (max-height: 420px) {
            .footer-note { display: none; }
        }

        /* Layout landscape: card con dos columnas internas (logo+texto | botones) */
        @media (orientation: landscape) and (max-height: 500px) {
            .card-inner {
                display: flex;
                align-items: center;
                gap: 32px;
            }
            .card-left  { flex: 1; min-width: 0; }
            .card-right { flex: 1; min-width: 0; }
        }
    </style>
</head>
<body>
<div class="page-bg">

    <!-- Orbes decorativos -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <!-- Card -->
    <div>
        <div class="card">
            <div class="card-inner">

                <!-- Columna izquierda: Logo + Textos -->
                <div class="card-left">
                    <!-- Logo -->
                    <div class="logo-wrap">
                        <div class="logo-box">
                            <svg viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Título -->
                    <h1 class="brand-title">JoralCore <span>ERP</span></h1>
                    <p class="brand-sub">Gestión Empresarial</p>
                </div>

                <!-- Columna derecha: Separador + Botones -->
                <div class="card-right">
                    <!-- Separador -->
                    <div class="divider"></div>

                    <!-- Botones -->
                    <div class="btn-wrap">

                        <!-- Ingreso Empresas -->
                        <a href="/company/login" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12"/>
                            </svg>
                            Ingreso Empresas
                        </a>

                        <!-- Ingreso Administrador -->
                        <a href="/admin/login" class="btn btn-secondary">
                            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                            Ingreso Administrador
                        </a>
                    </div>
                </div>

            </div><!-- /card-inner -->
        </div>

        <!-- Footer -->
        <p class="footer-note">
            &copy; {{ date('Y') }} JoralCore &middot; Todos los derechos reservados
        </p>
    </div>
</div>
</body>
</html>
