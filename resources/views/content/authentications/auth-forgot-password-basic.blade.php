@extends('layouts/blankLayout')

@section('title', __('Forgot Password'))

@section('page-style')
<style>
  /* ----------------------------------------------------
   * A.B.K Construction Supplies - Senior Auth Styles
   * Theme Tokens & Keyframe Animations (Same as Login)
   * ---------------------------------------------------- */
  :root {
    --auth-primary: #696cff;
    --auth-primary-glow: rgba(105, 108, 255, 0.45);
    --auth-secondary: #03c3ec;
    --auth-accent: #ffab00; /* Construction Amber */
    --auth-card-bg: rgba(22, 28, 45, 0.85);
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
    --auth-card-bg: rgba(255, 255, 255, 0.9);
    --auth-card-border: rgba(105, 108, 255, 0.2);
    --auth-card-shadow: 0 25px 65px -15px rgba(105, 108, 255, 0.22), 0 0 30px rgba(105, 108, 255, 0.1);
    --auth-text-title: #222943;
    --auth-text-body: #566a7f;
    --auth-input-bg: rgba(248, 250, 252, 0.92);
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
    0% { left: -160px; }
    100% { left: 105%; }
  }

  @keyframes truckTraverseSecondary {
    0% { left: -200px; }
    100% { left: 105%; }
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
    0% { right: -160px; }
    100% { right: 105%; }
  }

  @keyframes truckTraverseSecondaryRTL {
    0% { right: -200px; }
    100% { right: 105%; }
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
   * Top Quick Bar
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
   * Senior Glassmorphic Reset Card & Layout
   * ---------------------------------------------------- */
  .auth-card-outer {
    position: relative;
    width: 100%;
    max-width: 520px;
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
    padding: 2.5rem 2.25rem 2rem;
    box-shadow: var(--auth-card-shadow);
    overflow: hidden;
    transition: box-shadow 0.3s ease;
  }

  .auth-glass-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ffab00 0%, #696cff 50%, #03c3ec 100%);
    background-size: 200% 100%;
    animation: gradientShift 6s ease infinite;
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  /* ----------------------------------------------------
   * Stepper Indicator
   * ---------------------------------------------------- */
  .auth-stepper-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
    gap: 8px;
  }

  .step-node {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--auth-text-body);
    opacity: 0.55;
    transition: all 0.3s ease;
  }

  .step-node.active {
    opacity: 1;
    color: var(--auth-primary);
  }

  .step-node.completed {
    opacity: 0.9;
    color: #71dd37;
  }

  .step-bubble {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(105, 108, 255, 0.15);
    border: 1.5px solid currentColor;
    font-size: 0.82rem;
    font-weight: 700;
  }

  .step-node.completed .step-bubble {
    background: #71dd37;
    color: #111827;
    border-color: #71dd37;
  }

  .step-divider-line {
    width: 28px;
    height: 2px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 2px;
  }

  .light-style .step-divider-line {
    background: rgba(105, 108, 255, 0.2);
  }

  /* ----------------------------------------------------
   * Brand Header & Badges
   * ---------------------------------------------------- */
  .auth-brand-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 1.75rem;
  }

  .brand-logo-pod {
    position: relative;
    width: 76px;
    height: 76px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(255, 171, 0, 0.2) 0%, rgba(105, 108, 255, 0.2) 100%);
    border: 2px solid rgba(255, 171, 0, 0.45);
    border-radius: 22px;
    margin-bottom: 1rem;
    box-shadow: 0 10px 30px rgba(255, 171, 0, 0.25);
    transition: transform 0.3s ease;
  }

  .brand-logo-pod::after {
    content: '';
    position: absolute;
    inset: -6px;
    border-radius: 26px;
    border: 1.5px dashed rgba(105, 108, 255, 0.5);
    animation: rotateDashed 20s linear infinite;
  }

  @keyframes rotateDashed {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .brand-logo-pod i {
    font-size: 2.6rem;
    background: linear-gradient(135deg, #ffab00 0%, #696cff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .brand-main-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--auth-text-title);
    margin-bottom: 0.35rem;
  }

  .brand-tagline-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 12px;
    background: rgba(105, 108, 255, 0.12);
    border: 1px solid rgba(105, 108, 255, 0.3);
    border-radius: 20px;
    color: #696cff;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 0.65rem;
  }

  .brand-welcome-sub {
    font-size: 0.88rem;
    color: var(--auth-text-body);
    margin: 0;
    line-height: 1.5;
  }

  /* ----------------------------------------------------
   * Channel Selector Cards (Email vs SMS)
   * ---------------------------------------------------- */
  .channel-selector-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 1.5rem;
  }

  .channel-card {
    position: relative;
    padding: 14px 12px;
    border-radius: 16px;
    background: var(--auth-input-bg);
    border: 2px solid var(--auth-input-border);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 6px;
    user-select: none;
  }

  .channel-card:hover {
    border-color: rgba(105, 108, 255, 0.6);
    transform: translateY(-2px);
  }

  .channel-card.active {
    background: rgba(105, 108, 255, 0.12);
    border-color: #696cff;
    box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.25);
  }

  .light-style .channel-card.active {
    background: rgba(105, 108, 255, 0.08);
  }

  .channel-icon-pod {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.06);
    color: var(--auth-text-title);
    font-size: 1.5rem;
    transition: all 0.2s ease;
  }

  .channel-card.active .channel-icon-pod {
    background: #696cff;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(105, 108, 255, 0.4);
  }

  .channel-card-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--auth-text-title);
    margin: 0;
  }

  .channel-card-desc {
    font-size: 0.74rem;
    color: var(--auth-text-body);
    margin: 0;
    line-height: 1.3;
  }

  .channel-check-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #696cff;
    color: #fff;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
  }

  html[dir="rtl"] .channel-check-badge {
    right: auto;
    left: 8px;
  }

  .channel-card.active .channel-check-badge {
    display: flex;
  }

  /* ----------------------------------------------------
   * Form Controls & Inputs
   * ---------------------------------------------------- */
  .auth-field-group {
    margin-bottom: 1.35rem;
  }

  .auth-field-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.86rem;
    font-weight: 600;
    color: var(--auth-text-title);
    margin-bottom: 0.5rem;
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

  /* ----------------------------------------------------
   * 6-Digit OTP Boxes
   * ---------------------------------------------------- */
  .otp-inputs-wrapper {
    display: flex;
    gap: 8px;
    justify-content: center;
    direction: ltr !important;
    margin: 1.5rem 0;
  }

  .otp-digit-field {
    width: 48px;
    height: 56px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--auth-text-title);
    background: var(--auth-input-bg);
    border: 2px solid var(--auth-input-border);
    border-radius: 12px;
    outline: none;
    transition: all 0.2s ease;
  }

  .otp-digit-field:focus {
    border-color: #696cff;
    background: rgba(25, 32, 54, 0.9);
    box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.25);
    transform: scale(1.05);
  }

  .light-style .otp-digit-field:focus {
    background: #ffffff;
  }

  /* ----------------------------------------------------
   * Countdown & Demo Hint Badge
   * ---------------------------------------------------- */
  .otp-timer-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.85rem;
    color: var(--auth-text-body);
    margin-bottom: 1.5rem;
  }

  .demo-otp-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    background: rgba(255, 171, 0, 0.12);
    border: 1px solid rgba(255, 171, 0, 0.35);
    color: #ffab00;
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .demo-otp-pill:hover {
    background: rgba(255, 171, 0, 0.2);
    transform: scale(1.02);
  }

  /* Password Strength Meter */
  .pwd-strength-bar {
    height: 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 6px;
  }

  .pwd-strength-indicator {
    height: 100%;
    width: 0%;
    transition: width 0.3s ease, background-color 0.3s ease;
  }

  /* ----------------------------------------------------
   * High-Impact Submit Button
   * ---------------------------------------------------- */
  .auth-submit-btn {
    position: relative;
    width: 100%;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: linear-gradient(135deg, #696cff 0%, #4f46e5 50%, #3730a3 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(105, 108, 255, 0.5), 0 4px 12px rgba(105, 108, 255, 0.35);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .auth-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 32px -6px rgba(105, 108, 255, 0.65);
  }

  .auth-submit-btn::after {
    content: '';
    position: absolute;
    top: 0;
    left: -120%;
    width: 60%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
    transform: skewX(-25deg);
  }

  .auth-submit-btn:hover::after {
    left: 140%;
    transition: all 0.85s ease;
  }

  .auth-secondary-btn {
    width: 100%;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: transparent;
    border: 1px solid var(--auth-card-border);
    border-radius: 12px;
    color: var(--auth-text-body);
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.2s ease;
  }

  .auth-secondary-btn:hover {
    color: var(--auth-text-title);
    background: rgba(255, 255, 255, 0.05);
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
    font-size: 0.88rem;
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

  @keyframes shakeAlert {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
  }

  .auth-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--auth-text-body);
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 600;
    margin-top: 1.5rem;
    transition: all 0.2s ease;
  }

  .auth-back-link:hover {
    color: #696cff;
    transform: translateX(-4px);
  }

  html[dir="rtl"] .auth-back-link:hover {
    transform: translateX(4px);
  }

  /* Step Panels Visibility */
  .step-panel {
    display: none;
  }

  .step-panel.active {
    display: block;
    animation: fadeInStep 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
  }

  @keyframes fadeInStep {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Responsive */
  @media (max-width: 575.98px) {
    .auth-glass-card {
      padding: 2rem 1.25rem 1.75rem;
    }
    .channel-selector-grid {
      grid-template-columns: 1fr;
    }
    .otp-digit-field {
      width: 40px;
      height: 48px;
      font-size: 1.25rem;
    }
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

    <div class="animated-truck-carrier">
      <div class="truck-unit">
        <span class="truck-cargo-badge">A.B.K SUPPLIES</span>
        <i class="bx bxs-truck"></i>
        <div class="truck-headlight-beam"></div>
      </div>
    </div>

    <div class="animated-truck-carrier-secondary">
      <div class="truck-unit-secondary">
        <i class="bx bxs-cube"></i>
        <i class="bx bxs-truck"></i>
      </div>
    </div>
  </div>

  <!-- Top Quick Actions (Theme & Language Switch) -->
  <div class="auth-top-bar">
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

  <!-- Senior Glassmorphic Reset Password Card -->
  <div class="auth-card-outer">
    <div class="auth-glass-card">

      <!-- Header & Brand -->
      <div class="auth-brand-wrapper">
        <div class="brand-logo-pod">
          <i class="bx bx-shield-quarter"></i>
        </div>

        <h1 class="brand-main-title">
          {{ __('Forgot Password?') }} 🔒
        </h1>

        <div class="brand-tagline-pill">
          <i class="bx bxs-badge-check"></i>
          <span>{{ config('variables.NameSiteAr') }} • استعادة الحساب</span>
        </div>

        <p class="brand-welcome-sub" id="headerSubtitle">
          حدد وسيلة الاستعادة المفضلة عبر البريد الإلكتروني أو رسالة SMS
        </p>
      </div>

      <!-- Stepper Progress Nodes -->
      <div class="auth-stepper-wrapper">
        <div class="step-node active" id="stepNode1">
          <div class="step-bubble">1</div>
          <span>{{ __('Method') }}</span>
        </div>
        <div class="step-divider-line"></div>
        <div class="step-node" id="stepNode2">
          <div class="step-bubble">2</div>
          <span>OTP</span>
        </div>
        <div class="step-divider-line"></div>
        <div class="step-node" id="stepNode3">
          <div class="step-bubble">3</div>
          <span>{{ __('Password') }}</span>
        </div>
      </div>

      <!-- Live Dynamic Alert Banner -->
      <div class="auth-feedback-alert d-none" id="alertBox" role="alert">
        <i class="bx bx-info-circle" id="alertIcon"></i>
        <div id="alertMessage"></div>
      </div>

      <!-- =======================================================
           STEP 1: Select Channel & Enter Identifier
           ======================================================= -->
      <div class="step-panel active" id="panelStep1">
        <form id="formRequestCode" onsubmit="handleSendCode(event)">
          @csrf

          <!-- Channel Selector Cards -->
          <div class="channel-selector-grid">
            <!-- Email Card -->
            <div class="channel-card active" id="cardEmail" onclick="selectChannel('email')">
              <div class="channel-check-badge"><i class="bx bx-check"></i></div>
              <div class="channel-icon-pod">
                <i class="bx bx-envelope"></i>
              </div>
              <p class="channel-card-title">البريد الإلكتروني</p>
              <p class="channel-card-desc">إرسال رمز OTP إلى بريدك</p>
            </div>

            <!-- SMS Card -->
            <div class="channel-card" id="cardSms" onclick="selectChannel('sms')">
              <div class="channel-check-badge"><i class="bx bx-check"></i></div>
              <div class="channel-icon-pod">
                <i class="bx bx-mobile-alt"></i>
              </div>
              <p class="channel-card-title">رسالة نصية SMS</p>
              <p class="channel-card-desc">إرسال رمز OTP إلى هاتفك</p>
            </div>
          </div>

          <input type="hidden" name="channel" id="selectedChannel" value="email">

          <!-- Dynamic Input Field -->
          <div class="auth-field-group">
            <div class="auth-field-label">
              <span id="identifierLabel">{{ __('Email') }}</span>
              <i class="bx bx-envelope" id="identifierHeaderIcon"></i>
            </div>
            <div class="auth-input-container" id="identifierContainer">
              <span class="auth-input-prefix-icon"><i class="bx bx-at" id="identifierPrefixIcon"></i></span>
              <input 
                type="text" 
                class="auth-input-native" 
                id="identifierInput" 
                name="identifier" 
                placeholder="name@example.com" 
                required 
                autofocus
              />
            </div>
          </div>

          <!-- Submit Button -->
          <button class="auth-submit-btn" type="submit" id="btnSendCode">
            <i class="bx bx-paper-plane"></i>
            <span id="btnSendCodeText">إرسال رمز التحقق OTP</span>
            <div class="spinner-border spinner-border-sm text-light d-none" id="btnSendCodeSpinner" role="status"></div>
          </button>
        </form>

        <div class="text-center">
          <a href="{{ url('auth/login-basic') }}" class="auth-back-link">
            <i class="bx bx-arrow-back scaleX-n1-rtl"></i>
            <span>{{ __('Back to login') }}</span>
          </a>
        </div>
      </div>

      <!-- =======================================================
           STEP 2: Enter 6-Digit OTP Code
           ======================================================= -->
      <div class="step-panel" id="panelStep2">
        <div class="text-center mb-3">
          <p class="text-body mb-2" id="otpSentTargetText">تم إرسال رمز التحقق المكون من 6 أرقام إلى حسابك</p>
          
          <!-- Development Demo OTP Hint -->
          <div class="demo-otp-pill d-none" id="demoOtpHolder" onclick="autoFillDemoOtp()">
            <i class="bx bx-key"></i>
            <span>رمز الاختبار: <strong id="demoOtpCode"></strong> (انقر للتعبئة)</span>
          </div>
        </div>

        <form id="formVerifyOtp" onsubmit="handleVerifyOtp(event)">
          @csrf

          <!-- 6-Digit OTP Inputs -->
          <div class="otp-inputs-wrapper">
            <input type="text" maxlength="1" class="otp-digit-field" id="otp1" autofocus>
            <input type="text" maxlength="1" class="otp-digit-field" id="otp2">
            <input type="text" maxlength="1" class="otp-digit-field" id="otp3">
            <input type="text" maxlength="1" class="otp-digit-field" id="otp4">
            <input type="text" maxlength="1" class="otp-digit-field" id="otp5">
            <input type="text" maxlength="1" class="otp-digit-field" id="otp6">
          </div>

          <!-- Countdown Timer & Resend Button -->
          <div class="otp-timer-box">
            <span>الصلاحية: <strong id="timerDisplay">02:00</strong></span>
            <button type="button" class="btn btn-link p-0 text-primary fw-bold text-decoration-none" id="btnResendOtp" onclick="resendOtp()" disabled>
              إعادة إرسال الرمز
            </button>
          </div>

          <button class="auth-submit-btn" type="submit" id="btnVerifyOtp">
            <i class="bx bx-check-shield"></i>
            <span id="btnVerifyOtpText">تأكيد الرمز والمتابعة</span>
            <div class="spinner-border spinner-border-sm text-light d-none" id="btnVerifyOtpSpinner" role="status"></div>
          </button>

          <button type="button" class="auth-secondary-btn" onclick="goToStep(1)">
            <i class="bx bx-edit-alt"></i>
            <span>تغيير وسيلة الاستعادة أو الرقم</span>
          </button>
        </form>
      </div>

      <!-- =======================================================
           STEP 3: Set New Password
           ======================================================= -->
      <div class="step-panel" id="panelStep3">
        <form id="formResetPassword" onsubmit="handleResetPassword(event)">
          @csrf
          <input type="hidden" id="resetTokenInput" name="reset_token">

          <!-- New Password -->
          <div class="auth-field-group">
            <div class="auth-field-label">
              <span>{{ __('New Password') }}</span>
              <i class="bx bx-lock-alt"></i>
            </div>
            <div class="auth-input-container">
              <span class="auth-input-prefix-icon"><i class="bx bx-lock"></i></span>
              <input 
                type="password" 
                class="auth-input-native" 
                id="newPassword" 
                name="password" 
                placeholder="••••••••••••" 
                required 
                oninput="checkPasswordStrength(this.value)"
              />
              <button type="button" class="auth-password-toggle-btn" onclick="togglePasswordView('newPassword', 'iconToggleNew')">
                <i class="bx bx-hide" id="iconToggleNew"></i>
              </button>
            </div>
            <!-- Strength Bar -->
            <div class="pwd-strength-bar">
              <div class="pwd-strength-indicator" id="strengthIndicator"></div>
            </div>
            <small class="text-muted" id="strengthText">8 أحرف على الأقل مع أرقام وحروف</small>
          </div>

          <!-- Confirm Password -->
          <div class="auth-field-group">
            <div class="auth-field-label">
              <span>{{ __('Confirm Password') }}</span>
              <i class="bx bx-check-double"></i>
            </div>
            <div class="auth-input-container">
              <span class="auth-input-prefix-icon"><i class="bx bx-lock-open"></i></span>
              <input 
                type="password" 
                class="auth-input-native" 
                id="confirmPassword" 
                name="password_confirmation" 
                placeholder="••••••••••••" 
                required 
              />
              <button type="button" class="auth-password-toggle-btn" onclick="togglePasswordView('confirmPassword', 'iconToggleConfirm')">
                <i class="bx bx-hide" id="iconToggleConfirm"></i>
              </button>
            </div>
          </div>

          <!-- Submit Button -->
          <button class="auth-submit-btn" type="submit" id="btnResetPassword">
            <i class="bx bx-check-circle"></i>
            <span id="btnResetPasswordText">تحديث كلمة المرور والدخول</span>
            <div class="spinner-border spinner-border-sm text-light d-none" id="btnResetPasswordSpinner" role="status"></div>
          </button>
        </form>
      </div>

      <!-- =======================================================
           STEP 4: Success Screen
           ======================================================= -->
      <div class="step-panel" id="panelStep4">
        <div class="text-center py-3">
          <div class="mb-3">
            <i class="bx bx-badge-check text-success" style="font-size: 5rem; filter: drop-shadow(0 0 15px rgba(113, 221, 55, 0.4));"></i>
          </div>
          <h3 class="fw-bold text-white mb-2">تم تحديث كلمة المرور بنجاح!</h3>
          <p class="text-body mb-4">تم حفظ كلمة المرور الجديدة في نظام A.B.K. جاري تحويلك إلى صفحة تسجيل الدخول...</p>
          <a href="{{ url('auth/login-basic') }}" class="btn btn-primary px-4 py-2">
            <i class="bx bx-log-in me-1"></i> تسجيل الدخول الآن
          </a>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection

@section('page-script')
<script>
  let activeChannel = 'email';
  let activeIdentifier = '';
  let activeResetToken = '';
  let countdownSeconds = 120;
  let timerInterval = null;

  // Channel Selection: Email or SMS
  function selectChannel(channel) {
    activeChannel = channel;
    document.getElementById('selectedChannel').value = channel;

    const cardEmail = document.getElementById('cardEmail');
    const cardSms = document.getElementById('cardSms');
    const label = document.getElementById('identifierLabel');
    const headerIcon = document.getElementById('identifierHeaderIcon');
    const prefixIcon = document.getElementById('identifierPrefixIcon');
    const input = document.getElementById('identifierInput');

    hideAlert();

    if (channel === 'email') {
      cardEmail.classList.add('active');
      cardSms.classList.remove('active');
      label.textContent = 'البريد الإلكتروني';
      headerIcon.className = 'bx bx-envelope';
      prefixIcon.className = 'bx bx-at';
      input.type = 'email';
      input.placeholder = 'name@example.com';
      input.value = '';
    } else {
      cardSms.classList.add('active');
      cardEmail.classList.remove('active');
      label.textContent = 'رقم الهاتف (SMS)';
      headerIcon.className = 'bx bx-mobile-alt';
      prefixIcon.className = 'bx bx-phone';
      input.type = 'tel';
      input.placeholder = '0655443322';
      input.value = '';
    }
    input.focus();
  }

  // Stepper Transitions
  function goToStep(stepNumber) {
    hideAlert();

    // Panels
    document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
    document.getElementById(`panelStep${stepNumber}`).classList.add('active');

    // Node indicators
    const n1 = document.getElementById('stepNode1');
    const n2 = document.getElementById('stepNode2');
    const n3 = document.getElementById('stepNode3');

    n1.className = 'step-node';
    n2.className = 'step-node';
    n3.className = 'step-node';

    if (stepNumber === 1) {
      n1.classList.add('active');
      document.getElementById('headerSubtitle').textContent = 'حدد وسيلة الاستعادة المفضلة عبر البريد الإلكتروني أو رسالة SMS';
    } else if (stepNumber === 2) {
      n1.classList.add('completed');
      n2.classList.add('active');
      document.getElementById('headerSubtitle').textContent = 'أدخل رمز التحقق OTP المكون من 6 أرقام لتأكيد هويتك';
      document.getElementById('otp1').focus();
    } else if (stepNumber === 3) {
      n1.classList.add('completed');
      n2.classList.add('completed');
      n3.classList.add('active');
      document.getElementById('headerSubtitle').textContent = 'عيّن كلمة مرور قوية جديدة لحسابك';
      document.getElementById('newPassword').focus();
    } else if (stepNumber === 4) {
      n1.classList.add('completed');
      n2.classList.add('completed');
      n3.classList.add('completed');
      document.getElementById('headerSubtitle').textContent = 'تم إنجاز العملية بنجاح!';
    }
  }

  // Step 1: Send OTP Code
  async function handleSendCode(e) {
    e.preventDefault();
    hideAlert();

    const input = document.getElementById('identifierInput');
    const identifier = input.value.trim();

    if (!identifier) {
      showAlert('danger', 'يرجى إدخال البيانات المطلوبة');
      return;
    }

    const btn = document.getElementById('btnSendCode');
    const btnText = document.getElementById('btnSendCodeText');
    const spinner = document.getElementById('btnSendCodeSpinner');

    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'جاري الإرسال...';

    try {
      const response = await fetch("{{ route('password.send-code') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': "{{ csrf_token() }}",
        },
        body: JSON.stringify({
          channel: activeChannel,
          identifier: identifier,
        }),
      });

      const data = await response.json();

      if (response.ok && data.success) {
        activeIdentifier = identifier;

        document.getElementById('otpSentTargetText').textContent = 
          `تم إرسال رمز التحقق إلى: ${data.masked_identifier || identifier}`;

        // Handle Demo OTP in local/debug environment
        if (data.demo_otp) {
          document.getElementById('demoOtpCode').textContent = data.demo_otp;
          document.getElementById('demoOtpHolder').classList.remove('d-none');
        } else {
          document.getElementById('demoOtpHolder').classList.add('d-none');
        }

        showAlert('success', data.message);
        startCountdown();
        goToStep(2);
      } else {
        showAlert('danger', data.message || 'تعذر إرسال الرمز، يرجى التحقق من صحة البيانات');
      }
    } catch (err) {
      showAlert('danger', 'حدث خطأ في الاتصال بالخادم، يرجى المحاولة لاحقاً');
    } finally {
      btn.disabled = false;
      spinner.classList.add('d-none');
      btnText.textContent = 'إرسال رمز التحقق OTP';
    }
  }

  // Step 2: Verify OTP
  async function handleVerifyOtp(e) {
    e.preventDefault();
    hideAlert();

    const otp = getCombinedOtp();
    if (otp.length !== 6) {
      showAlert('danger', 'يرجى إدخال جميع أرقام الرمز المكون من 6 أرقام');
      return;
    }

    const btn = document.getElementById('btnVerifyOtp');
    const btnText = document.getElementById('btnVerifyOtpText');
    const spinner = document.getElementById('btnVerifyOtpSpinner');

    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'جاري التحقق...';

    try {
      const response = await fetch("{{ route('password.verify-otp') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': "{{ csrf_token() }}",
        },
        body: JSON.stringify({
          channel: activeChannel,
          identifier: activeIdentifier,
          otp: otp,
        }),
      });

      const data = await response.json();

      if (response.ok && data.success) {
        activeResetToken = data.reset_token;
        document.getElementById('resetTokenInput').value = data.reset_token;
        clearInterval(timerInterval);
        showAlert('success', data.message);
        goToStep(3);
      } else {
        showAlert('danger', data.message || 'رمز التحقق غير صحيح أو منتهي الصلاحية');
      }
    } catch (err) {
      showAlert('danger', 'تعذر الاتصال بالخادم أثناء التحقق من الرمز');
    } finally {
      btn.disabled = false;
      spinner.classList.add('d-none');
      btnText.textContent = 'تأكيد الرمز والمتابعة';
    }
  }

  // Step 3: Reset Password
  async function handleResetPassword(e) {
    e.preventDefault();
    hideAlert();

    const newPwd = document.getElementById('newPassword').value;
    const confirmPwd = document.getElementById('confirmPassword').value;

    if (newPwd.length < 8) {
      showAlert('danger', 'يجب ألا تقل كلمة المرور عن 8 أحرف');
      return;
    }

    if (newPwd !== confirmPwd) {
      showAlert('danger', 'كلمتا المرور غير متطابقتين، يرجى التأكد وإعادة المحاولة');
      return;
    }

    const btn = document.getElementById('btnResetPassword');
    const btnText = document.getElementById('btnResetPasswordText');
    const spinner = document.getElementById('btnResetPasswordSpinner');

    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'جاري التحديث...';

    try {
      const response = await fetch("{{ route('password.reset-action') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': "{{ csrf_token() }}",
        },
        body: JSON.stringify({
          channel: activeChannel,
          identifier: activeIdentifier,
          reset_token: activeResetToken,
          password: newPwd,
          password_confirmation: confirmPwd,
        }),
      });

      const data = await response.json();

      if (response.ok && data.success) {
        goToStep(4);
        setTimeout(() => {
          window.location.href = "{{ route('login') }}";
        }, 2200);
      } else {
        showAlert('danger', data.message || 'تعذر تحديث كلمة المرور');
      }
    } catch (err) {
      showAlert('danger', 'حدث خطأ غير متوقع أثناء حفظ كلمة المرور');
    } finally {
      btn.disabled = false;
      spinner.classList.add('d-none');
      btnText.textContent = 'تحديث كلمة المرور والدخول';
    }
  }

  // Resend OTP
  function resendOtp() {
    handleSendCode(new Event('submit'));
  }

  // OTP Digit Boxes Handling
  const otpFields = [
    document.getElementById('otp1'),
    document.getElementById('otp2'),
    document.getElementById('otp3'),
    document.getElementById('otp4'),
    document.getElementById('otp5'),
    document.getElementById('otp6')
  ];

  otpFields.forEach((field, index) => {
    field.addEventListener('input', function (e) {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value && index < 5) {
        otpFields[index + 1].focus();
      }
      if (getCombinedOtp().length === 6) {
        document.getElementById('formVerifyOtp').dispatchEvent(new Event('submit'));
      }
    });

    field.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && !this.value && index > 0) {
        otpFields[index - 1].focus();
      }
    });

    field.addEventListener('paste', function (e) {
      e.preventDefault();
      const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
      if (pasted.length >= 6) {
        for (let i = 0; i < 6; i++) {
          otpFields[i].value = pasted[i];
        }
        document.getElementById('formVerifyOtp').dispatchEvent(new Event('submit'));
      }
    });
  });

  function getCombinedOtp() {
    return otpFields.map(f => f.value).join('');
  }

  function autoFillDemoOtp() {
    const code = document.getElementById('demoOtpCode').textContent.trim();
    if (code && code.length === 6) {
      for (let i = 0; i < 6; i++) {
        otpFields[i].value = code[i];
      }
      document.getElementById('formVerifyOtp').dispatchEvent(new Event('submit'));
    }
  }

  // Countdown Timer
  function startCountdown() {
    clearInterval(timerInterval);
    countdownSeconds = 120;
    const display = document.getElementById('timerDisplay');
    const resendBtn = document.getElementById('btnResendOtp');
    resendBtn.disabled = true;

    function update() {
      const minutes = Math.floor(countdownSeconds / 60);
      const seconds = countdownSeconds % 60;
      display.textContent = `${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

      if (countdownSeconds <= 0) {
        clearInterval(timerInterval);
        display.textContent = 'منتهي';
        resendBtn.disabled = false;
      }
      countdownSeconds--;
    }

    update();
    timerInterval = setInterval(update, 1000);
  }

  // Password Strength Meter
  function checkPasswordStrength(val) {
    const indicator = document.getElementById('strengthIndicator');
    const text = document.getElementById('strengthText');
    let score = 0;

    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    if (val.length === 0) {
      indicator.style.width = '0%';
      text.textContent = '8 أحرف على الأقل مع أرقام وحروف';
      text.className = 'text-muted';
    } else if (score <= 1) {
      indicator.style.width = '33%';
      indicator.style.backgroundColor = '#ff3e1d';
      text.textContent = 'كلمة مرور ضعيفة';
      text.className = 'text-danger';
    } else if (score === 2 || score === 3) {
      indicator.style.width = '66%';
      indicator.style.backgroundColor = '#ffab00';
      text.textContent = 'كلمة مرور مقبولة';
      text.className = 'text-warning';
    } else {
      indicator.style.width = '100%';
      indicator.style.backgroundColor = '#71dd37';
      text.textContent = 'كلمة مرور قوية جداً ومحصنة';
      text.className = 'text-success';
    }
  }

  // Password View Toggle
  function togglePasswordView(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'bx bx-show';
    } else {
      input.type = 'password';
      icon.className = 'bx bx-hide';
    }
  }

  // Alert Banners
  function showAlert(type, msg) {
    const alertBox = document.getElementById('alertBox');
    const alertMessage = document.getElementById('alertMessage');
    const alertIcon = document.getElementById('alertIcon');

    alertBox.className = `auth-feedback-alert ${type === 'danger' ? 'alert-danger-custom' : 'alert-success-custom'}`;
    alertIcon.className = type === 'danger' ? 'bx bx-error-circle' : 'bx bx-check-circle';
    alertMessage.textContent = msg;
    alertBox.classList.remove('d-none');
  }

  function hideAlert() {
    document.getElementById('alertBox').classList.add('d-none');
  }
</script>
@endsection
