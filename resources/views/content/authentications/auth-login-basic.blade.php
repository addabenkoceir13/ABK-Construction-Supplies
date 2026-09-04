@extends('layouts/blankLayout')

@section('title', __('Login Basic'))

@section('page-style')
<style>
  /* ----------------------------------------------------
   * A.B.K Construction Supplies - Senior Auth Styles
   * Theme Tokens & Keyframe Animations
   * ---------------------------------------------------- */
  :root {
    --auth-primary: #696cff;
    --auth-primary-glow: rgba(105, 108, 255, 0.45);
    --auth-secondary: #03c3ec;
    --auth-accent: #ffab00; /* Construction Amber */
    --auth-card-bg: rgba(22, 28, 45, 0.82);
    --auth-card-border: rgba(255, 255, 255, 0.12);
    --auth-card-shadow: 0 25px 65px -15px rgba(0, 0, 0, 0.65), 0 0 40px rgba(105, 108, 255, 0.18);
    --auth-text-title: #ffffff;
    --auth-text-body: #a5b4fc;
    --auth-input-bg: rgba(15, 20, 36, 0.65);
    --auth-input-border: rgba(255, 255, 255, 0.14);
    --auth-input-focus-border: #696cff;
    --auth-input-color: #f1f5f9;
  }

  /* Light Style Theme Overrides */
  .light-style {
    --auth-card-bg: rgba(255, 255, 255, 0.88);
    --auth-card-border: rgba(105, 108, 255, 0.2);
    --auth-card-shadow: 0 25px 65px -15px rgba(105, 108, 255, 0.22), 0 0 30px rgba(105, 108, 255, 0.1);
    --auth-text-title: #222943;
    --auth-text-body: #566a7f;
    --auth-input-bg: rgba(248, 250, 252, 0.9);
    --auth-input-border: rgba(105, 108, 255, 0.25);
    --auth-input-focus-border: #696cff;
    --auth-input-color: #334155;
  }

  /* Reset layout constraints from standard page-auth.css */
  .authentication-wrapper.authentication-basic .authentication-inner:before,
  .authentication-wrapper.authentication-basic .authentication-inner:after {
    display: none !important;
  }

  .auth-main-viewport {
    position: relative;
    min-height: 100vh;
    width: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1rem 4.5rem;
    background-color: #0b0f19;
    z-index: 1;
    font-family: 'Public Sans', 'Cairo', sans-serif;
  }

  .light-style .auth-main-viewport {
    background-color: #f4f6fb;
  }

  /* ----------------------------------------------------
   * Ambient Glow Mesh & High-Tech Grid
   * ---------------------------------------------------- */
  .auth-ambient-mesh {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
  }

  .mesh-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(95px);
    opacity: 0.55;
    animation: orbPulse 16s ease-in-out infinite alternate;
  }

  .light-style .mesh-orb {
    opacity: 0.35;
    filter: blur(85px);
  }

  .mesh-orb-1 {
    top: -12%;
    left: -10%;
    width: 550px;
    height: 550px;
    background: radial-gradient(circle, #696cff 0%, rgba(105, 108, 255, 0) 70%);
  }

  .mesh-orb-2 {
    bottom: -15%;
    right: -10%;
    width: 650px;
    height: 650px;
    background: radial-gradient(circle, #03c3ec 0%, rgba(3, 195, 236, 0) 70%);
    animation-delay: -5s;
  }

  .mesh-orb-3 {
    top: 35%;
    right: 15%;
    width: 420px;
    height: 420px;
    background: radial-gradient(circle, #ffab00 0%, rgba(255, 171, 0, 0) 70%);
    opacity: 0.35;
    animation-delay: -9s;
  }

  @keyframes orbPulse {
    0% { transform: scale(1) translate(0, 0); }
    50% { transform: scale(1.15) translate(30px, -25px); }
    100% { transform: scale(0.92) translate(-20px, 35px); }
  }

  .auth-grid-pattern {
    position: fixed;
    inset: 0;
    pointer-events: none;
    background-image: 
      linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
      linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(circle at center, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.1) 85%);
    -webkit-mask-image: radial-gradient(circle at center, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.1) 85%);
    z-index: 0;
  }

  .light-style .auth-grid-pattern {
    background-image: 
      linear-gradient(to right, rgba(105, 108, 255, 0.07) 1px, transparent 1px),
      linear-gradient(to bottom, rgba(105, 108, 255, 0.07) 1px, transparent 1px);
  }

  /* ----------------------------------------------------
   * Floating Construction Supplies Icons
   * ---------------------------------------------------- */
  .floating-icons-container {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
  }

  .float-item {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.22);
    border-radius: 18px;
    transition: all 0.3s ease;
  }

  .light-style .float-item {
    color: rgba(105, 108, 255, 0.35);
  }

  .float-item i {
    filter: drop-shadow(0 0 14px currentColor);
  }

  /* Floating Nodes with distinct paths and speeds */
  .float-node-1 {
    top: 14%;
    left: 10%;
    font-size: 3.2rem;
    color: rgba(105, 108, 255, 0.4);
    animation: floatDrift1 14s ease-in-out infinite;
  }

  .float-node-2 {
    top: 22%;
    right: 12%;
    font-size: 2.8rem;
    color: rgba(255, 171, 0, 0.45);
    animation: floatDrift2 16s ease-in-out infinite;
  }

  .float-node-3 {
    bottom: 28%;
    left: 8%;
    font-size: 2.6rem;
    color: rgba(3, 195, 236, 0.4);
    animation: floatDrift3 18s ease-in-out infinite;
  }

  .float-node-4 {
    bottom: 30%;
    right: 9%;
    font-size: 3rem;
    color: rgba(113, 221, 55, 0.4);
    animation: floatDrift1 20s ease-in-out infinite reverse;
  }

  .float-node-5 {
    top: 55%;
    left: 16%;
    font-size: 2.2rem;
    color: rgba(255, 171, 0, 0.35);
    animation: floatDrift2 15s ease-in-out infinite reverse;
  }

  .float-node-6 {
    top: 68%;
    right: 18%;
    font-size: 2.4rem;
    color: rgba(105, 108, 255, 0.35);
    animation: floatDrift3 19s ease-in-out infinite;
  }

  .float-node-7 {
    top: 8%;
    right: 38%;
    font-size: 2rem;
    color: rgba(3, 195, 236, 0.3);
    animation: floatDrift1 13s ease-in-out infinite;
  }

  @keyframes floatDrift1 {
    0% { transform: translateY(0) rotate(0deg) scale(1); }
    50% { transform: translateY(-28px) translateX(18px) rotate(12deg) scale(1.08); }
    100% { transform: translateY(0) rotate(0deg) scale(1); }
  }

  @keyframes floatDrift2 {
    0% { transform: translateY(0) rotate(0deg) scale(1); }
    50% { transform: translateY(26px) translateX(-20px) rotate(-14deg) scale(0.95); }
    100% { transform: translateY(0) rotate(0deg) scale(1); }
  }

  @keyframes floatDrift3 {
    0% { transform: translateY(0) rotate(0deg) scale(0.98); }
    50% { transform: translateY(-35px) translateX(-15px) rotate(18deg) scale(1.1); }
    100% { transform: translateY(0) rotate(0deg) scale(0.98); }
  }

  /* ----------------------------------------------------
   * Construction Highway / Supply Truck Track (Bottom)
   * ---------------------------------------------------- */
  .truck-highway-track {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 72px;
    z-index: 1;
    pointer-events: none;
    overflow: hidden;
    background: linear-gradient(to top, rgba(11, 15, 25, 0.92) 0%, rgba(11, 15, 25, 0.6) 65%, transparent 100%);
    border-top: 1px solid rgba(105, 108, 255, 0.18);
  }

  .light-style .truck-highway-track {
    background: linear-gradient(to top, rgba(235, 240, 250, 0.95) 0%, rgba(235, 240, 250, 0.6) 65%, transparent 100%);
    border-top: 1px solid rgba(105, 108, 255, 0.15);
  }

  /* Moving road divider stripes */
  .road-lane-stripes {
    position: absolute;
    bottom: 24px;
    left: 0;
    width: 200%;
    height: 3px;
    background: repeating-linear-gradient(
      90deg,
      rgba(255, 171, 0, 0.75) 0px,
      rgba(255, 171, 0, 0.75) 35px,
      transparent 35px,
      transparent 65px
    );
    animation: roadMove 2.2s linear infinite;
  }

  @keyframes roadMove {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
  }

  /* Animated Trucks cruising the track */
  .animated-truck-carrier {
    position: absolute;
    bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: truckTraverse 16s linear infinite;
    filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.45));
  }

  .animated-truck-carrier-secondary {
    position: absolute;
    bottom: 18px;
    display: flex;
    align-items: center;
    gap: 6px;
    animation: truckTraverseSecondary 22s linear infinite;
    animation-delay: -8s;
  }

  @keyframes truckTraverse {
    0% {
      left: -160px;
    }
    100% {
      left: 105%;
    }
  }

  @keyframes truckTraverseSecondary {
    0% {
      left: -200px;
    }
    100% {
      left: 105%;
    }
  }

  /* RTL Physics for Highway and Trucks */
  html[dir="rtl"] .animated-truck-carrier {
    animation: truckTraverseRTL 16s linear infinite;
    left: auto;
  }

  html[dir="rtl"] .animated-truck-carrier-secondary {
    animation: truckTraverseSecondaryRTL 22s linear infinite;
    animation-delay: -8s;
    left: auto;
  }

  html[dir="rtl"] .truck-unit {
    border-radius: 16px 12px 8px 8px;
  }

  html[dir="rtl"] .truck-unit i.bxs-truck {
    transform: scaleX(-1);
    display: inline-block;
  }

  html[dir="rtl"] .truck-unit-secondary i.bxs-truck {
    transform: scaleX(-1);
    display: inline-block;
  }

  html[dir="rtl"] .truck-headlight-beam {
    left: -75px;
    right: auto;
    transform: translateY(-50%) scaleX(-1);
  }

  html[dir="rtl"] .road-lane-stripes {
    animation: roadMoveRTL 2.2s linear infinite;
  }

  @keyframes truckTraverseRTL {
    0% {
      right: -160px;
    }
    100% {
      right: 105%;
    }
  }

  @keyframes truckTraverseSecondaryRTL {
    0% {
      right: -200px;
    }
    100% {
      right: 105%;
    }
  }

  @keyframes roadMoveRTL {
    from { transform: translateX(0); }
    to { transform: translateX(50%); }
  }

  .truck-unit {
    position: relative;
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #ffab00 0%, #ff8f00 100%);
    color: #1e1e2d;
    padding: 6px 14px;
    border-radius: 12px 16px 8px 8px;
    box-shadow: 0 4px 15px rgba(255, 171, 0, 0.35);
    animation: truckSuspension 0.4s ease-in-out infinite alternate;
  }

  .truck-unit i {
    font-size: 1.65rem;
    color: #111827;
  }

  .truck-cargo-badge {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-right: 6px;
    color: #111827;
    white-space: nowrap;
  }

  /* Truck Headlight Beam */
  .truck-headlight-beam {
    position: absolute;
    right: -75px;
    top: 50%;
    transform: translateY(-50%);
    width: 75px;
    height: 38px;
    background: linear-gradient(90deg, rgba(255, 235, 59, 0.55) 0%, rgba(255, 235, 59, 0) 100%);
    clip-path: polygon(0% 40%, 100% 0%, 100% 100%, 0% 60%);
    pointer-events: none;
  }

  @keyframes truckSuspension {
    0% { transform: translateY(0); }
    100% { transform: translateY(-2px); }
  }

  .truck-unit-secondary {
    background: linear-gradient(135deg, #696cff 0%, #4338ca 100%);
    padding: 5px 12px;
    border-radius: 10px 14px 6px 6px;
    box-shadow: 0 4px 15px rgba(105, 108, 255, 0.35);
    color: #fff;
    animation: truckSuspension 0.45s ease-in-out infinite alternate;
  }

  .truck-unit-secondary i {
    font-size: 1.45rem;
    color: #ffffff;
  }

  /* ----------------------------------------------------
   * Top Quick Controls Bar (Theme & Locale)
   * ---------------------------------------------------- */
  .auth-top-bar {
    position: fixed;
    top: 20px;
    right: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 10;
  }

  @if (Session::get('locale') == 'ar')
  .auth-top-bar {
    right: auto;
    left: 25px;
  }
  @endif

  .auth-quick-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--auth-text-title);
    background: var(--auth-card-bg);
    border: 1px solid var(--auth-card-border);
    border-radius: 30px;
    backdrop-filter: blur(14px);
    text-decoration: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  }

  .auth-quick-btn:hover {
    color: #fff;
    background: #696cff;
    border-color: #696cff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 108, 255, 0.4);
  }

  /* ----------------------------------------------------
   * Senior Glassmorphism Login Card
   * ---------------------------------------------------- */
  .auth-card-outer {
    position: relative;
    width: 100%;
    max-width: 470px;
    z-index: 5;
    animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  @keyframes cardEntrance {
    0% {
      opacity: 0;
      transform: translateY(28px) scale(0.96);
    }
    100% {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .auth-glass-card {
    position: relative;
    background: var(--auth-card-bg);
    border: 1px solid var(--auth-card-border);
    backdrop-filter: blur(28px) saturate(190%);
    -webkit-backdrop-filter: blur(28px) saturate(190%);
    border-radius: 26px;
    padding: 2.75rem 2.25rem 2.25rem;
    box-shadow: var(--auth-card-shadow);
    overflow: hidden;
    transition: box-shadow 0.3s ease;
  }

  /* Luminous top neon accent border */
  .auth-glass-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #696cff 0%, #03c3ec 50%, #ffab00 100%);
    background-size: 200% 100%;
    animation: gradientShift 6s ease infinite;
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  /* ----------------------------------------------------
   * Brand Header & Animated Badge
   * ---------------------------------------------------- */
  .auth-brand-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 2rem;
  }

  .brand-logo-pod {
    position: relative;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(105, 108, 255, 0.25) 0%, rgba(3, 195, 236, 0.2) 100%);
    border: 2px solid rgba(105, 108, 255, 0.4);
    border-radius: 24px;
    margin-bottom: 1.25rem;
    box-shadow: 0 10px 30px rgba(105, 108, 255, 0.3);
    transition: transform 0.3s ease;
  }

  .brand-logo-pod:hover {
    transform: rotate(5deg) scale(1.05);
  }

  .brand-logo-pod::after {
    content: '';
    position: absolute;
    inset: -6px;
    border-radius: 28px;
    border: 1.5px dashed rgba(255, 171, 0, 0.5);
    animation: rotateDashed 20s linear infinite;
  }

  @keyframes rotateDashed {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .brand-logo-pod i {
    font-size: 2.75rem;
    background: linear-gradient(135deg, #696cff 0%, #03c3ec 50%, #ffab00 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: drop-shadow(0 2px 8px rgba(105, 108, 255, 0.5));
  }

  .brand-main-title {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--auth-text-title);
    margin-bottom: 0.35rem;
    letter-spacing: -0.3px;
  }

  .brand-tagline-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    background: rgba(255, 171, 0, 0.12);
    border: 1px solid rgba(255, 171, 0, 0.3);
    border-radius: 20px;
    color: #ffab00;
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
  }

  .brand-welcome-sub {
    font-size: 0.92rem;
    color: var(--auth-text-body);
    margin: 0;
    line-height: 1.5;
  }

  /* ----------------------------------------------------
   * Alerts & Messages
   * ---------------------------------------------------- */
  .auth-feedback-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 14px;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    font-weight: 500;
    animation: shakeAlert 0.4s ease-in-out;
  }

  .auth-feedback-alert.alert-danger-custom {
    background: rgba(255, 62, 29, 0.12);
    border: 1px solid rgba(255, 62, 29, 0.35);
    color: #ff3e1d;
  }

  .auth-feedback-alert.alert-success-custom {
    background: rgba(113, 221, 55, 0.12);
    border: 1px solid rgba(113, 221, 55, 0.35);
    color: #71dd37;
  }

  .auth-feedback-alert i {
    font-size: 1.4rem;
    flex-shrink: 0;
  }

  @keyframes shakeAlert {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
  }

  /* ----------------------------------------------------
   * Form Controls & Floating Focus
   * ---------------------------------------------------- */
  .auth-field-group {
    margin-bottom: 1.35rem;
  }

  .auth-field-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--auth-text-title);
    margin-bottom: 0.5rem;
  }

  .auth-field-label i {
    font-size: 1rem;
    color: #696cff;
  }

  .auth-input-container {
    position: relative;
    display: flex;
    align-items: center;
    background: var(--auth-input-bg);
    border: 1.5px solid var(--auth-input-border);
    border-radius: 14px;
    transition: all 0.25s ease;
    overflow: hidden;
  }

  .auth-input-container:focus-within {
    border-color: var(--auth-input-focus-border);
    background: rgba(25, 32, 54, 0.85);
    box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.22);
  }

  .light-style .auth-input-container:focus-within {
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.18);
  }

  .auth-input-prefix-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 14px;
    color: #696cff;
    font-size: 1.25rem;
    pointer-events: none;
  }

  .auth-input-native {
    width: 100%;
    height: 48px;
    background: transparent !important;
    border: none !important;
    color: var(--auth-input-color) !important;
    font-size: 0.95rem;
    padding: 0 10px;
    outline: none !important;
    box-shadow: none !important;
  }

  .auth-input-native::placeholder {
    color: rgba(165, 180, 252, 0.45);
  }

  .light-style .auth-input-native::placeholder {
    color: rgba(100, 116, 139, 0.5);
  }

  .auth-password-toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 14px;
    background: transparent;
    border: none;
    color: var(--auth-text-body);
    font-size: 1.25rem;
    cursor: pointer;
    transition: color 0.2s ease;
  }

  .auth-password-toggle-btn:hover {
    color: #696cff;
  }

  /* Remember & Forgot Row */
  .auth-options-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    font-size: 0.88rem;
  }

  .auth-checkbox-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: var(--auth-text-body);
    user-select: none;
  }

  .auth-custom-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    accent-color: #696cff;
    cursor: pointer;
  }

  .auth-forgot-link {
    color: #696cff;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
  }

  .auth-forgot-link:hover {
    color: #03c3ec;
    text-decoration: underline;
  }

  /* ----------------------------------------------------
   * High-Impact Gradient Submit Button with Shine Sweep
   * ---------------------------------------------------- */
  .auth-submit-btn {
    position: relative;
    width: 100%;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: linear-gradient(135deg, #696cff 0%, #4f46e5 50%, #3730a3 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 14px;
    font-size: 1.05rem;
    font-weight: 700;
    cursor: pointer;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(105, 108, 255, 0.5), 0 4px 12px rgba(105, 108, 255, 0.35);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .auth-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 32px -6px rgba(105, 108, 255, 0.65), 0 6px 16px rgba(105, 108, 255, 0.45);
    color: #ffffff !important;
  }

  .auth-submit-btn:active {
    transform: translateY(1px);
    box-shadow: 0 6px 16px -4px rgba(105, 108, 255, 0.4);
  }

  /* Shine Sweep Light Effect */
  .auth-submit-btn::after {
    content: '';
    position: absolute;
    top: 0;
    left: -120%;
    width: 60%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
    transform: skewX(-25deg);
    transition: none;
  }

  .auth-submit-btn:hover::after {
    left: 140%;
    transition: all 0.85s ease;
  }

  .auth-submit-btn i {
    font-size: 1.35rem;
    transition: transform 0.2s ease;
  }

  .auth-submit-btn:hover i {
    transform: scale(1.15);
  }

  /* ----------------------------------------------------
   * Card Footer & Security Badge
   * ---------------------------------------------------- */
  .auth-card-footer {
    margin-top: 1.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--auth-card-border);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: var(--auth-text-body);
    font-size: 0.82rem;
    text-align: center;
  }

  .auth-card-footer i {
    color: #71dd37;
    font-size: 1.15rem;
  }

  /* Responsive Adjustments */
  @media (max-width: 575.98px) {
    .auth-main-viewport {
      padding: 1.5rem 1rem 4rem;
    }

    .auth-glass-card {
      padding: 2rem 1.5rem 1.75rem;
      border-radius: 20px;
    }

    .brand-logo-pod {
      width: 68px;
      height: 68px;
    }

    .brand-logo-pod i {
      font-size: 2.2rem;
    }

    .brand-main-title {
      font-size: 1.35rem;
    }

    .auth-top-bar {
      top: 12px;
      right: 12px;
    }

    @if (Session::get('locale') == 'ar')
    .auth-top-bar {
      left: 12px;
      right: auto;
    }
    @endif
  }
</style>
@endsection

@section('content')
<div class="auth-main-viewport">

  <!-- Ambient Glowing Mesh Gradient Orbs -->
  <div class="auth-ambient-mesh">
    <div class="mesh-orb mesh-orb-1"></div>
    <div class="mesh-orb mesh-orb-2"></div>
    <div class="mesh-orb mesh-orb-3"></div>
  </div>

  <!-- Geometric Construction Grid -->
  <div class="auth-grid-pattern"></div>

  <!-- Floating Construction & Supplies Icons -->
  <div class="floating-icons-container" aria-hidden="true">
    <div class="float-item float-node-1" title="شاحنة نقل"><i class="bx bxs-truck"></i></div>
    <div class="float-item float-node-2" title="مخروط سلامة البناء"><i class="bx bxs-traffic-cone"></i></div>
    <div class="float-item float-node-3" title="مواد بناء"><i class="bx bxs-cube-alt"></i></div>
    <div class="float-item float-node-4" title="طرود ومستلزمات"><i class="bx bxs-package"></i></div>
    <div class="float-item float-node-5" title="أدوات ومعدات"><i class="bx bxs-wrench"></i></div>
    <div class="float-item float-node-6" title="مباني ومنشآت"><i class="bx bxs-building"></i></div>
    <div class="float-item float-node-7" title="شاحنة لوجستية"><i class="bx bxs-truck"></i></div>
  </div>

  <!-- Construction Highway / Moving Truck Track -->
  <div class="truck-highway-track" aria-hidden="true">
    <div class="road-lane-stripes"></div>

    <!-- Lead Heavy Supply Truck -->
    <div class="animated-truck-carrier">
      <div class="truck-unit">
        <span class="truck-cargo-badge">A.B.K SUPPLIES</span>
        <i class="bx bxs-truck"></i>
        <div class="truck-headlight-beam"></div>
      </div>
    </div>

    <!-- Secondary Logistics Hauler -->
    <div class="animated-truck-carrier-secondary">
      <div class="truck-unit-secondary">
        <i class="bx bxs-cube"></i>
        <i class="bx bxs-truck"></i>
      </div>
    </div>
  </div>

  <!-- Top Quick Actions (Theme & Language Switch) -->
  <div class="auth-top-bar">
    <!-- Theme Toggle -->
    @if (Session::get('theme') == 'dark')
      <a href="{{ url('theme/light') }}" class="auth-quick-btn" title="{{ __('Light Mode') }}">
        <i class="bx bx-sun text-warning"></i>
        <span class="d-none d-sm-inline">{{ __('Light') }}</span>
      </a>
    @else
      <a href="{{ url('theme/dark') }}" class="auth-quick-btn" title="{{ __('Dark Mode') }}">
        <i class="bx bx-moon text-primary"></i>
        <span class="d-none d-sm-inline">{{ __('Dark') }}</span>
      </a>
    @endif

    <!-- Language Toggle -->
    @if (Session::get('locale') == 'ar')
      <a href="{{ url('lang/en') }}" class="auth-quick-btn" title="English">
        <i class="bx bx-globe"></i>
        <span>English</span>
      </a>
    @else
      <a href="{{ url('lang/ar') }}" class="auth-quick-btn" title="العربية">
        <i class="bx bx-globe"></i>
        <span>العربية</span>
      </a>
    @endif
  </div>

  <!-- Senior Glassmorphic Card Container -->
  <div class="auth-card-outer">
    <div class="auth-glass-card">

      <!-- Brand Logo & Header -->
      <div class="auth-brand-wrapper">
        <div class="brand-logo-pod">
          <i class="bx bxs-truck"></i>
        </div>

        <h1 class="brand-main-title">
          {{ config('app.locale') == 'en' ? config('variables.templateName') : config('variables.templateNameAr') }}
        </h1>

        <div class="brand-tagline-pill">
          <i class="bx bxs-badge-check"></i>
          <span>{{ config('variables.NameSiteAr') }} • مستلزمات البناء</span>
        </div>

        <p class="brand-welcome-sub">
          {{ __('Welcome') }}! 👋 {{ __('Please sign-in to your account') }}
        </p>
      </div>

      <!-- Feedback / Alert Notifications -->
      @if (session('error'))
        <div class="auth-feedback-alert alert-danger-custom" role="alert">
          <i class="bx bx-error-circle"></i>
          <div>{{ session('error') }}</div>
        </div>
      @elseif ($errors->any())
        <div class="auth-feedback-alert alert-danger-custom" role="alert">
          <i class="bx bx-error-circle"></i>
          <div>
            @foreach ($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        </div>
      @elseif (session('success') && !str_contains(strtolower(session('success')), 'not valid'))
        <div class="auth-feedback-alert alert-success-custom" role="alert">
          <i class="bx bx-check-circle"></i>
          <div>{{ session('success') }}</div>
        </div>
      @elseif (session('success') && str_contains(strtolower(session('success')), 'not valid'))
        <div class="auth-feedback-alert alert-danger-custom" role="alert">
          <i class="bx bx-error-circle"></i>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      <!-- Authentication Form -->
      <form id="formAuthentication" action="{{ url('/auth/login-action') }}" method="POST" autocomplete="on">
        @csrf

        <!-- Email Field -->
        <div class="auth-field-group">
          <div class="auth-field-label">
            <span>{{ __('Email') }}</span>
            <i class="bx bx-envelope"></i>
          </div>
          <div class="auth-input-container @error('email') border-danger @enderror">
            <span class="auth-input-prefix-icon"><i class="bx bx-user"></i></span>
            <input 
              type="email" 
              class="auth-input-native" 
              id="email" 
              name="email" 
              value="{{ old('email') }}" 
              placeholder="{{ __('Enter your email') }}" 
              required 
              autocomplete="email" 
              autofocus
            />
          </div>
        </div>

        <!-- Password Field -->
        <div class="auth-field-group">
          <div class="auth-field-label">
            <span>{{ __('Password') }}</span>
            <a href="{{ url('auth/forgot-password-basic') }}" class="auth-forgot-link">
              {{ __('Forgot Password?') }}
            </a>
          </div>
          <div class="auth-input-container @error('password') border-danger @enderror">
            <span class="auth-input-prefix-icon"><i class="bx bx-lock-alt"></i></span>
            <input 
              type="password" 
              class="auth-input-native" 
              id="password" 
              name="password" 
              placeholder="••••••••••••" 
              required 
              autocomplete="current-password"
            />
            <button type="button" class="auth-password-toggle-btn" id="togglePasswordBtn" title="إظهار / إخفاء كلمة المرور">
              <i class="bx bx-hide" id="passwordToggleIcon"></i>
            </button>
          </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="auth-options-row">
          <label class="auth-checkbox-label" for="remember-me">
            <input class="auth-custom-checkbox" type="checkbox" id="remember-me" name="remember" value="1" checked />
            <span>{{ __('Remember Me') }}</span>
          </label>
        </div>

        <!-- Submit Button -->
        <button class="auth-submit-btn" type="submit" id="btnSubmitLogin">
          <i class="bx bx-log-in-circle"></i>
          <span id="btnSubmitText">{{ __('Sign in') }}</span>
          <div class="spinner-border spinner-border-sm text-light d-none" id="btnSubmitSpinner" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </button>

      </form>

      <!-- Security Guarantee & Footer -->
      <div class="auth-card-footer">
        <i class="bx bx-shield-quarter"></i>
        <span>نظام محمي ومشفر • عدة بن قصير سفيان</span>
      </div>

    </div>
  </div>

</div>
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Interactive Password Show/Hide Toggle
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');

    if (toggleBtn && passwordInput && toggleIcon) {
      toggleBtn.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          toggleIcon.classList.remove('bx-hide');
          toggleIcon.classList.add('bx-show');
        } else {
          passwordInput.type = 'password';
          toggleIcon.classList.remove('bx-show');
          toggleIcon.classList.add('bx-hide');
        }
      });
    }

    // Submit Button Loading State
    const authForm = document.getElementById('formAuthentication');
    const submitBtn = document.getElementById('btnSubmitLogin');
    const submitText = document.getElementById('btnSubmitText');
    const submitSpinner = document.getElementById('btnSubmitSpinner');

    if (authForm && submitBtn) {
      authForm.addEventListener('submit', function () {
        if (authForm.checkValidity()) {
          setTimeout(function () {
            submitBtn.style.pointerEvents = 'none';
            submitBtn.style.opacity = '0.85';
            if (submitSpinner) submitSpinner.classList.remove('d-none');
            if (submitText) submitText.textContent = 'جاري التحقق...';
          }, 50);
        }
      });
    }
  });
</script>
@endsection
