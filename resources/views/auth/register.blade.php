<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Optimanage">
    <meta name="author" content="Optimanage">
    <meta name="keywords" content="OptiManage">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="{{ asset('img/icons/icon-48x48.png') }}" />

    <link rel="canonical" href="https://demo-basic.adminkit.io/pages-sign-in.html" />

    <title>{{env('APP_NAME')}}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', sans-serif;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
        }
        .card-body {
            padding: 2.5rem;
        }
        .btn-primary {
            background-color: #3b7ddd;
            border-color: #3b7ddd;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2c66c3;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(59,125,221,0.3);
        }
        .form-control {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #3b7ddd;
            box-shadow: 0 0 0 0.2rem rgba(59,125,221,0.25);
        }
        .text-center h1 {
            color: #2c3e50;
            font-weight: 600;
        }
        .text-center .lead {
            color: #6c757d;
        }
    </style>
</head>

<body>
<main class="d-flex w-100">
    <div class="container d-flex flex-column">
        <div class="row vh-100">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                <div class="d-table-cell align-middle">

                    <div class="text-center mt-4">
                        <h1 class="h2">Get started</h1>
                        <p class="lead">
                            Start creating the best possible user experience for your customers.
                        </p>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="m-sm-4">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input class="form-control form-control-lg" type="text" name="name" placeholder="Enter your name" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input class="form-control form-control-lg" type="text" name="phone_number" placeholder="Enter your phone number" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Profile Image</label>
                                        <input class="form-control form-control-lg" type="text" name="profile_image" placeholder="Enter profile image URL" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role ID</label>
                                        <input class="form-control form-control-lg" type="number" name="role_id" placeholder="Enter role ID" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Enter password" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input class="form-control form-control-lg" type="password" name="password_confirmation" placeholder="Confirm password" required />
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-lg btn-primary">Sign up</button>
                                    </div>
                                </form>
                                <div class="text-center mt-3">
                                    <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="{{ asset('js/app.js') }}"></script>

</body>

</html>
