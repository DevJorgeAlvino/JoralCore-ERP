import { Head } from '@inertiajs/react';

export default function Welcome() {
    return (
        <>
            <Head title="JoralERP">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap"
                    rel="stylesheet"
                />
                <style>{`
                    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

                    body {
                        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                        background: #09090b;
                        color: #fafafa;
                        min-height: 100vh;
                        overflow: hidden;
                    }

                    .welcome-wrapper {
                        position: relative;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        padding: 2rem;
                    }

                    /* Animated gradient orbs */
                    .orb {
                        position: absolute;
                        border-radius: 50%;
                        filter: blur(120px);
                        opacity: 0.15;
                        animation: float 20s ease-in-out infinite;
                    }

                    .orb-1 {
                        width: 600px;
                        height: 600px;
                        background: linear-gradient(135deg, #6366f1, #8b5cf6);
                        top: -200px;
                        right: -100px;
                        animation-delay: 0s;
                    }

                    .orb-2 {
                        width: 500px;
                        height: 500px;
                        background: linear-gradient(135deg, #3b82f6, #06b6d4);
                        bottom: -150px;
                        left: -100px;
                        animation-delay: -7s;
                    }

                    .orb-3 {
                        width: 300px;
                        height: 300px;
                        background: linear-gradient(135deg, #a855f7, #ec4899);
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        animation-delay: -14s;
                    }

                    @keyframes float {
                        0%, 100% { transform: translate(0, 0) scale(1); }
                        25% { transform: translate(30px, -40px) scale(1.05); }
                        50% { transform: translate(-20px, 20px) scale(0.95); }
                        75% { transform: translate(40px, 30px) scale(1.02); }
                    }

                    /* Subtle grid overlay */
                    .grid-overlay {
                        position: absolute;
                        inset: 0;
                        background-image:
                            linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
                        background-size: 60px 60px;
                        mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
                        -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
                    }

                    /* Card container */
                    .card {
                        position: relative;
                        z-index: 10;
                        width: 100%;
                        max-width: 440px;
                        padding: 3rem 2.5rem;
                        border-radius: 24px;
                        background: rgba(255, 255, 255, 0.03);
                        border: 1px solid rgba(255, 255, 255, 0.06);
                        backdrop-filter: blur(40px);
                        -webkit-backdrop-filter: blur(40px);
                        animation: cardIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    @keyframes cardIn {
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }

                    /* Logo */
                    .logo-container {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-bottom: 2rem;
                    }

                    .logo-icon {
                        width: 48px;
                        height: 48px;
                        border-radius: 14px;
                        background: linear-gradient(135deg, #6366f1, #8b5cf6);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 0 30px rgba(99, 102, 241, 0.3);
                    }

                    .logo-icon svg {
                        width: 24px;
                        height: 24px;
                        color: white;
                    }

                    /* Text */
                    .title {
                        font-size: 1.75rem;
                        font-weight: 700;
                        text-align: center;
                        letter-spacing: -0.025em;
                        margin-bottom: 0.5rem;
                        background: linear-gradient(to bottom, #fafafa, #a1a1aa);
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                        background-clip: text;
                    }

                    .subtitle {
                        font-size: 0.9rem;
                        text-align: center;
                        color: #71717a;
                        margin-bottom: 2.5rem;
                        line-height: 1.5;
                        font-weight: 400;
                    }

                    /* Divider */
                    .divider {
                        height: 1px;
                        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
                        margin-bottom: 2rem;
                    }

                    /* Buttons */
                    .buttons {
                        display: flex;
                        flex-direction: column;
                        gap: 0.875rem;
                    }

                    .btn {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.75rem;
                        width: 100%;
                        padding: 0.875rem 1.5rem;
                        border-radius: 14px;
                        font-size: 0.9rem;
                        font-weight: 500;
                        font-family: inherit;
                        text-decoration: none;
                        cursor: pointer;
                        border: none;
                        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                        position: relative;
                        overflow: hidden;
                    }

                    .btn-admin {
                        background: linear-gradient(135deg, #6366f1, #8b5cf6);
                        color: white;
                        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.25), inset 0 1px 0 rgba(255,255,255,0.1);
                    }

                    .btn-admin:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4), inset 0 1px 0 rgba(255,255,255,0.15);
                    }

                    .btn-admin:active {
                        transform: translateY(0);
                    }

                    .btn-company {
                        background: rgba(255, 255, 255, 0.04);
                        color: #e4e4e7;
                        border: 1px solid rgba(255, 255, 255, 0.08);
                    }

                    .btn-company:hover {
                        background: rgba(255, 255, 255, 0.07);
                        border-color: rgba(255, 255, 255, 0.12);
                        transform: translateY(-2px);
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                    }

                    .btn-company:active {
                        transform: translateY(0);
                    }

                    .btn-icon {
                        width: 20px;
                        height: 20px;
                        flex-shrink: 0;
                        opacity: 0.9;
                    }

                    /* Footer */
                    .footer {
                        margin-top: 2rem;
                        text-align: center;
                        font-size: 0.75rem;
                        color: #3f3f46;
                        letter-spacing: 0.02em;
                    }

                    /* Responsive */
                    @media (max-width: 480px) {
                        .card {
                            padding: 2rem 1.5rem;
                            border-radius: 20px;
                        }
                        .title {
                            font-size: 1.5rem;
                        }
                    }
                `}</style>
            </Head>

            <div className="welcome-wrapper">
                {/* Background orbs */}
                <div className="orb orb-1" />
                <div className="orb orb-2" />
                <div className="orb orb-3" />

                {/* Grid overlay */}
                <div className="grid-overlay" />

                {/* Main card */}
                <div className="card">
                    {/* Logo */}
                    <div className="logo-container">
                        <div className="logo-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth={2}
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            >
                                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                <path d="M2 17l10 5 10-5" />
                                <path d="M2 12l10 5 10-5" />
                            </svg>
                        </div>
                    </div>

                    {/* Title */}
                    <h1 className="title">JoralERP</h1>
                    <p className="subtitle">Selecciona tu portal de acceso</p>

                    {/* Divider */}
                    <div className="divider" />

                    {/* Buttons */}
                    <div className="buttons">
                        <a href="/admin/login" className="btn btn-admin" id="btn-admin-login">
                            <svg
                                className="btn-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth={2}
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            >
                                <path d="M12 15V3m0 12l-4-4m4 4l4-4" />
                                <path d="M2 17l.621 2.485A2 2 0 004.561 21h14.878a2 2 0 001.94-1.515L22 17" />
                            </svg>
                            Panel Administrativo
                        </a>

                        <a href="/company/login" className="btn btn-company" id="btn-company-login">
                            <svg
                                className="btn-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth={2}
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            >
                                <path d="M3 21h18" />
                                <path d="M5 21V7l8-4v18" />
                                <path d="M19 21V11l-6-4" />
                                <path d="M9 9v.01" />
                                <path d="M9 12v.01" />
                                <path d="M9 15v.01" />
                                <path d="M9 18v.01" />
                            </svg>
                            Portal de Empresa
                        </a>
                    </div>

                    {/* Footer */}
                    <div className="footer">
                        © {new Date().getFullYear()} JoralERP — Todos los derechos reservados
                    </div>
                </div>
            </div>
        </>
    );
}
