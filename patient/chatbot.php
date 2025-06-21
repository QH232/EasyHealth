<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/chatbot.css">
        
    <title>AI Assistant</title>
    <style>
        .dashbord-tables {
            animation: transitionIn-Y-over 0.5s;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
    }
    
    include("../connection.php");
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];
    ?>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">All Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-ai menu-active menu-icon-ai-active">
                        <a href="chatbot.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">AI Assistant</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <div class="chatbot-wrapper">
                <div class="chatbot-card">
                    <h1 class="chatbot-title">
                        <span class="easy">Easy</span><span class="health">Health</span>
                        <span class="ai-assistant">AI Assistant</span>
                    </h1>
                    <div class="chatbot-messages" id="messages">
                        <div class="chatbot-message chatbot-message-bot">
                            <span class="chatbot-message-author">EasyHealth AI Assistant</span>
                            <p>Hello! How can I assist you today?</p>
                        </div>
                    </div>
                    <div class="chatbot-input-row">
                        <input type="text" id="inputPrompt" class="chatbot-input" placeholder="Type your question..." />
                        <button class="chatbot-send-btn" id="sendPromptBtn" onclick="GetResponse()">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    function GetResponse() {
        const sendPromptBtn = document.getElementById('sendPromptBtn');
        const inputPrompt = document.getElementById('inputPrompt');
        const messages = document.getElementById('messages');
        const promptValue = inputPrompt.value.trim();
        if (!promptValue) return;

        sendPromptBtn.innerHTML = `Sending...`;
        messages.innerHTML += `
        <div class="chatbot-message chatbot-message-user">
            <span class="chatbot-message-author">You</span>
            <p>${promptValue}</p>
        </div>
        `;
        messages.scrollTop = messages.scrollHeight;

        fetch('chatbot-api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            role: "You act as a EasyHealth AI Assistant",
            prompt: promptValue
        })
        })
        .then(res => res.json())
        .then(data => {
        let reply = "Sorry, I couldn't process your request.";
        if (data && data.reply) reply = data.reply;
        else if (data && data.error) reply = data.error;
        messages.innerHTML += `
            <div class="chatbot-message chatbot-message-bot">
            <span class="chatbot-message-author">EasyHealth AI Assistant</span>
            <p>${reply}</p>
            </div>
        `;
        inputPrompt.value = "";
        sendPromptBtn.innerHTML = "Send";
        messages.scrollTop = messages.scrollHeight;
        })
        .catch(error => {
        messages.innerHTML += `
            <div class="chatbot-message chatbot-message-bot">
            <span class="chatbot-message-author">EasyHealth AI Assistant</span>
            <p>Sorry, there was an error connecting to the AI.</p>
            </div>
        `;
        sendPromptBtn.innerHTML = "Send";
        messages.scrollTop = messages.scrollHeight;
        });
    }
    document.getElementById('inputPrompt').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') GetResponse();
    });
</script>
</body>
</html>
