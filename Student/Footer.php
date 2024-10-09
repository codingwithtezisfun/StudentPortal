<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Example</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .custom-footer-container {
            background-color: #36454f;
            padding: 20px;
            border-top: 1px solid #dee2e6;
            color: white;
            font-size: 15px;
            font-weight: bold;
        }
        .custom-footer-container h5 {
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .footer-inner-top {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .footer-inner-bottom {
            padding: 20px;
            text-align: center;
            color: white;
        }
        .footer-copyright,
        .footer-designed-by {
            margin: 5px 0;
        }
        .footer-hacker-icon {
            display: inline-block;
            font-size: 24px;
            color: black;
            text-decoration: none;
            margin-left: 10px;
        }
        .footer-clearfix {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .footer-social-icons {
            display: flex;
            gap: 15px;
            font-size: 30px;
        }
        .footer-social-icons a {
            text-decoration: none;
        }
        .footer-social-icons .fa-twitter {
            color: #1DA1F2; /* Twitter blue */
        }
        .footer-social-icons .fa-facebook {
            color: #1877F2; /* Facebook blue */
        }
        .footer-social-icons .fa-tiktok {
            color: white; /* TikTok black */
        }
        .footer-social-icons .fa-youtube {
            color: #FF0000; /* YouTube red */
        }
        .footer-social-icons .fa-whatsapp {
            color: #25D366; /* WhatsApp green */
        }
        .footer-social-icons .fa-envelope {
            color: #E74C3C; /* Email light blue */
        }
        @media (max-width: 768px) {
            .footer-inner-top {
                grid-template-columns: 1fr;
            }
            .clickable-div {
                margin-top: 30px;
            }
            .footer-right {
                margin-top: 20px;
            }
        }
        .clickable-div {
            display: block; 
            width: 100%;
            height:70px;
            padding: 10px; 
            text-decoration: none; 
            color: inherit; 
            border-radius: 10px;
            border: solid 2px;
            animation: flash 2s infinite;
        }
        @keyframes flash {
    0% {
        border-color: magenta;
    }
    50% {
        border-color: red;
    }
    100% {
        border-color: magenta;
    }
    }
        .clickable-div:hover {
            background-color: #f2f2f2; 
            cursor: pointer; 
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Form content here -->

        <div class="custom-footer-container">
            <div class="row footer-clearfix">
                <div class="col-md-4 col-sm-12">
                    <h6>Find KISE On</h6>
                    <div class="footer-social-icons">
                        <a href="https://twitter.com" target="_blank" class="fab fa-twitter" aria-label="Twitter"></a>
                        <a href="https://facebook.com" target="_blank" class="fab fa-facebook" aria-label="Facebook"></a>
                        <a href="https://tiktok.com" target="_blank" class="fab fa-tiktok" aria-label="TikTok"></a>
                        <a href="https://youtube.com" target="_blank" class="fab fa-youtube" aria-label="YouTube"></a>
                        <a href="https://wa.me/2540729151582" target="_blank" class="fab fa-whatsapp" aria-label="WhatsApp"></a>
                        <a href="mailto:eantez254@gmail.com" class="fas fa-envelope" aria-label="Email"></a>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 footer-right">
                    <div class="footer-copyright">© 2024 Kenya Institute of Software Engineering</div>
                </div>
                <a href="Designer.php" class="clickable-div col-md-4 col-sm-12" style="display: block;">
                    <div class="footer-designed-by">Portal designed and created by <span class="footer-hacker-icon">&#x1f575;</span></div>
                </a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
