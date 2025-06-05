<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyHealth AI Assistant</title>
    <link rel="stylesheet" href="../css/chatbot.css">
  </head>
  <body>
    <div class="chatbot-container">
      <h1 class="chatbot-title">
        <span class="easy">Easy</span><span class="health">Health</span>
        <span class="ai-assistant">AI Assistant</span>
      </h1>
      <div class="chatbot-card">
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
