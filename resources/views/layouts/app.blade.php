<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Keffect Marketplace' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #1f2937; }
        header, footer { background: #111827; color: #fff; padding: 1rem 2rem; }
        main { padding: 2rem; }
        .container { max-width: 1100px; margin: 0 auto; }
        .grid { display: grid; gap: 1rem; }
        .grid-3 { grid-template-columns: repeat(3, minmax(0,1fr)); }
        .card { background: white; border-radius: 10px; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .badge { display: inline-block; padding: .2rem .5rem; border-radius: 999px; font-size: .8rem; background: #e5e7eb; }
        nav a { color: #fff; text-decoration: none; margin-right: 1rem; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { text-align: left; padding: .75rem; border-bottom: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <nav>
            <a href="/">Storefront</a>
            <a href="/products">Products</a>
            <a href="/account">Account</a>
            <a href="/admin">Admin</a>
        </nav>
    </div>
</header>
<main>
    <div class="container">
        @yield('content')
    </div>
</main>
<footer>
    <div class="container">Keffect Marketplace • Laravel + LSL/L$ integration scaffold</div>
</footer>
</body>
</html>
