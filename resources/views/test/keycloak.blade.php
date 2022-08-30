@extends('layout')
@section('header')
    <div class="ml-5">
        <h1>Test Keycloak OAuth</h1>
        This page simulates a customer instance page with Authorize Button that will probably be implemented in
        module=welcome&action=settings
    </div>
@endsection
@section('headerBgImageStyle')
@endsection
@section('content')
    <div class="d-flex flex-column mx-auto align-items-center w-50">
        <div class="p-2">
            <form action="{{ $url }}" method="get">
                <header class="row">
                    <h2>The simulated instance will call this-server with the inputs as request parameter</h2>
                    <label for="vodafone_url"><code>vodafone_url</code></label>
                    <input name="vodafone_url" value="{{ $vodafoneCustomer }}">
                    <label for="scope"><code>scope</code></label>
                    <input name="scope" value="{{ $scope }}">

                </header>
                <div id="main" class="left mt-2">
                    <div>
                        <button type="submit" class="btn btn-success">Authorize</button>
                    </div>
                </div>
            </form>

            <form action="{{ $refreshUrl }}" method="post">
                <header class="row">
                    <h2>Refresh token</h2>
                    <label for="vodafone_url"><code>vodafone_url</code></label>
                    <input name="vodafone_url" value="{{ $vodafoneCustomer }}">
                    <label for="refresh_token"><code>Refresh token</code></label>
                    <input name="refresh_token" value="{{ $refreshToken }}">
                    <label for="scope"><code>scope</code></label>
                    <input name="scope" value="{{ $scope }}">

                </header>
                <div id="main" class="left mt-2">
                    <div>
                        <button type="submit" class="btn btn-success">Refresh Token</button>
                    </div>
                </div>
            </form>

            <form action="{{ $revokeUrl }}" method="post">
                <header class="row">
                    <h2>Revoke token</h2>
                    <label for="access_token"><code>Access token</code></label>
                    <input name="access_token" value="{{ $accessToken }}">
                    <label for="refresh_token"><code>Refresh token</code></label>
                    <input name="refresh_token" value="{{ $refreshToken }}">
                </header>
                <div id="main" class="left mt-2">
                    <div>
                        <button type="submit" class="btn btn-success">Revoke Token</button>
                    </div>
                </div>
            </form>
            <form action="{{ $getTokenUrl }}" method="post">
                <header class="row">
                    <h2>Get token</h2>
                    <label for="exchange_verifier"><code>Exchange Verifier</code></label>
                    <input name="exchange_verifier" value="{{ $exchangeVerifier }}">
                    <label for="vodafone_session"><code>Xentral session</code></label>
                    <input name="vodafone_session" value="{{ $xsession }}">
                </header>
                <div id="main" class="left mt-2">
                    <div>
                        <button type="submit" class="btn btn-success">Get Token</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
