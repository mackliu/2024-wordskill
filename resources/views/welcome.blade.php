<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>module C</title>
    <link rel="stylesheet" href="{{ asset('public/css/bootstrap.css') }}">
    <style>
        div.cover{
            width: 100vw;
            position: relative;
            height: 65vh;
        }
        div.cover-effect{
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            --y: 50%;
            --x: 50%;
            mask-image: radial-gradient(circle at var(--x) var(--y), transparent, rgba(0,0,0,0.3) 300px);
            background: rgba(255,255,255,0.3);
        }
        img.cover-img{
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        div.legend{
            width: 600px;
            height: 100px;
            position: relative;
            margin: auto;
            transform: translateY(-50px);
        }
        div.info{
            height: 500px;
        }
    </style>
</head>
<body>
<div class="cover">
    <div class="cover-effect"></div>
    <img src="{{ asset('public/heritages/images/2024-05-02-lyon-place-bellecour.jpg') }}" alt="" class="cover-img">
    <div class="legend bg-dark text-light p-3 ln-1">
        <h3>Test</h3>
    </div>


    <div class="d-flex mx-auto justify-content-center">
        <div class="card info w-50 shadow rounded-0 z-2"></div>
        <div class="card shadow rounded-0 p-4 fs-5 z-1" style="width: 300px;height: 150px">
            <p>Date: 2024-09-01</p>
            <p></p>
            <p>Draft: true</p>
        </div>
    </div>
</div>
<script>
    let url = "{{ asset('public/heritages/images') }}"
    console.log(url)
    const CoverEffect = document.querySelector(".cover-effect");
    CoverEffect.addEventListener('mousemove',(e)=>{
        const rect = CoverEffect.getBoundingClientRect();
        const x = e.clientX - rect.left
        const y = e.clientY - rect.top
        CoverEffect.style.setProperty('--x',`${x}px`)
        CoverEffect.style.setProperty('--y',`${y}px`)
    })
</script>
</body>
</html>
