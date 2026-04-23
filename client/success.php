<?php
// success.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Success</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

/* BACKGROUND */
body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:radial-gradient(circle at top left,#0b1220,#020617);
    color:#fff;
}

/* CARD */
.card{
    background:rgba(17,24,39,0.85);
    padding:40px 30px;
    width:380px;
    border-radius:18px;
    text-align:center;
    box-shadow:0 0 40px rgba(0,0,0,0.6);
    border:1px solid rgba(255,255,255,0.05);
}

/* ICON */
.icon{
    font-size:50px;
    margin-bottom:15px;
}

/* TITLE */
h2{
    font-size:22px;
    margin-bottom:8px;
}

/* SUBTEXT */
p{
    font-size:14px;
    color:#9ca3af;
    margin-bottom:25px;
    line-height:1.5;
}

/* BUTTON */
.btn{
    display:block;
    width:100%;
    padding:12px;
    background:#16a34a;
    border:none;
    border-radius:10px;
    color:#fff;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
}

.btn:hover{
    background:#22c55e;
}
</style>

</head>
<body>

<div class="card">

    <div class="icon">🎉</div>

    <h2>Application Submitted!</h2>

    <p>
        Admin will review your details. Login after approval.
    </p>

    <a href="login.php" class="btn">
        Go to Login →
    </a>

</div>

</body>
</html>