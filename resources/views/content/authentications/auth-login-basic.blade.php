@extends('layouts/blankLayout')

@section('title', __('Login Basic'))

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Register -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center">
            <a href="{{url('/')}}" class="app-brand-link gap-2">
              <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'#696cff'])</span>
              <span class="app-brand-text demo text-body fw-bolder">{{config('app.locale') == 'en' ?  config('variables.templateName') :  config('variables.templateNameAr')}}</span>
            </a>
          </div>
          <!-- /Logo -->

          <h4 class="mb-2">{{ __('Welcome') }} {{ config('app.locale') == 'en' ?  config('variables.templateName') :  config('variables.templateNameAr')}}! 👋</h4>


          <form id="formAuthentication" class="mb-3" action="{{url('/auth/login-action')}}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">{{ __('Email') }}</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="{{ __('Enter your email') }}" autofocus>
            </div>
            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">{{ __('Password') }}</label>
                <a href="{{url('auth/forgot-password-basic')}}">
                  <small>{{ __('Forgot Password?') }}</small>
                </a>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me"  name="remember" checked>
                <label class="form-check-label" for="remember-me">
                  {{ __('Remember Me') }}
                </label>
              </div>
            </div>
            <div class="mb-3">
              <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Sign in') }}</button>
            </div>
          </form>

          {{-- <p class="text-center">
            <span>New on our platform?</span>
            <a href="{{url('auth/register-basic')}}">
              <span>Create an account</span>
            </a>
          </p> --}}
        </div>
      </div>
    </div>
    <!-- /Register -->
  </div>
</div>
</div>
@endsection
