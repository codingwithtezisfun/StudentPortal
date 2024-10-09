<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Information</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-image: url('../images/programmer4.jpg');
            background-size: cover;
            background-position: center;
            margin-top: 20px;
             margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
           height: calc(2 * 100vh);
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 0; 
        }


        .container {
            z-index: 1;
        }

        .jumbotron {
            background-color: transparent;
            padding: 2rem 1rem;
        }

        .icon {
            font-size: 40px;
            color: white;
        }

        .description {
            margin-top: 10px;
            color: #00ff00;
            font-size: 18px;
            line-height: 1.6;
        }

        .contact-icons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .contact-icons a {
            margin-right: 20px;
            font-size: 60px;
            color: white;
        }

        .contact-icons .fa-whatsapp {
            color: #25D366;
        }

        .contact-icons .fa-envelope {
            color: #E74C3C;
        }

        /* Flashing border and button styles */
        .btn-about {
            margin-top: 20px;
            background-color: blue;
            color: white;
            border: 2px solid;
            animation: flash-border 1.5s infinite;
            width: 250px;
            height:50px;
            border-radius:30px;
        }

        @keyframes flash-border {
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

        .form-container {
            display: none;
             margin-top: 10px;
            color: #00ff00;
            font-size: 18px;
            line-height: 1.6;
        }

        .back-btn {
           margin-top: 20px;
            background-color: blue;
            color: white;
            border: 2px solid;
            animation: flash-border 1.5s infinite;
            width: 250px;
            height:50px;
            border-radius:30px;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <div class="container text-center">
        <h4 class="display-6" style="color: #00ff00;">Designed by Ian Kipchirchir Tarus</h4>

        <div class="contact-icons">
            <a href="https://wa.me/2540729151582" target="_blank" class="fab fa-whatsapp" aria-label="WhatsApp"></a>
            <a href="mailto:eantez254@gmail.com" class="fas fa-envelope" aria-label="Email"></a>
        </div>

        <div class="description">
    <h2 class="text-white" style="color: #00ff00;">Who Am I?  <i class="icon">&#x1f575;</i></h2>
        <p>
            I am a passionate web developer and technology enthusiast with experience in designing and developing both web and desktop applications. My expertise spans front-end and back-end technologies, with a strong focus on creating responsive and user-friendly interfaces.
        </p>
        <p>
            I enjoy solving complex problems and am dedicated to delivering high-quality solutions that meet client requirements.
        </p>
        <p>
            I am proficient in developing desktop applications using Java, leveraging libraries like Java Swing and JavaFX for rich user interfaces, and JDBC for database connectivity. Additionally, I have extensive experience with Java EE to create scalable and maintainable online applications. I use robust frameworks such as Spring Boot, Spring, Hibernate, JSF, and Struts to build complete software solutions—from sophisticated front-end designs to powerful back-end systems—ensuring excellent performance and reliability.
        </p>
        <p>
            For front-end technologies, I utilize React and Bootstrap alongside HTML and CSS to build responsive and dynamic user interfaces.
        </p>
        <p>
            I am also proficient in PHP, capable of creating full-scale applications. This portal is a testament to my skills, where I have used PHP and Laravel to manage operations through efficient handling of POST requests.
        </p>
        <p>
            I studied at the Kenya Institute of Software Engineering, and this portal is a tribute to the institution for the invaluable knowledge and skills I gained during my time there.
        </p>
                <button id="aboutBtn" class="btn btn-about">About the Portal</button>
    </div>

        <!-- Dynamic Content Area -->
        <div id="portalInfo" class="form-container">
            <p>The Student Portal was developed using a combination of PHP, Bootstrap, HTML, and CSS for front-end development. These technologies work together to create a responsive, modern, and user-friendly interface.</p>
            
            <p>On the back-end, PHP was the primary language used to handle all server-side logic, including data processing and interaction with the database. AJAX and jQuery were employed to create seamless, asynchronous interactions, allowing data to be submitted without refreshing the page. Additionally, both PHP's POST and GET methods were utilized to efficiently handle form submissions and data requests.</p>
            
            <p>The database was designed using MySQL, which provides robust and scalable data storage, ensuring the portal can handle large amounts of student information securely and efficiently.</p>
            
            <p>JavaScript played a crucial role in enhancing the user experience by adding dynamic animations and interactive elements, making the portal more engaging and responsive to user actions.</p>
            
            <button id="backBtn" class="btn btn-secondary back-btn">Back</button>
        </div>

    </div>

    <!-- Bootstrap and jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

   <script>
    $(document).ready(function() {
        // Show the form and hide original content on button click
        $('#aboutBtn').click(function() {
            $(this).hide();
            $('.description, .contact-icons').hide();
            $('#portalInfo').show();

            // Scroll to the top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Show original content and hide form on back button click
        $('#backBtn').click(function() {
            $('#portalInfo').hide();
            $('#aboutBtn').show();
            $('.description, .contact-icons').show();

            // Scroll to the top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>

</body>
</html>
