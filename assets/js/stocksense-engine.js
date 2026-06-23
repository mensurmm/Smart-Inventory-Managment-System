document.addEventListener("DOMContentLoaded", () => {
    const chatStream = document.getElementById("chat-stream");
    const userInput = document.getElementById("ai-user-input");
    const sendBtn = document.getElementById("send-btn");
    const chips = document.querySelectorAll(".query-chip");

    // 1. Function to append message bubbles to the UI
    function appendMessage(text, sender) {
        const messageDiv = document.createElement("div");
        messageDiv.classList.add("message", sender);

        const bubbleDiv = document.createElement("div");
        bubbleDiv.classList.add("bubble");
        bubbleDiv.innerText = text;

        messageDiv.appendChild(bubbleDiv);
        chatStream.appendChild(messageDiv);

        // Auto-scroll to the latest message
        chatStream.scrollTop = chatStream.scrollHeight;
    }

    // 2. Main handler to send data via AJAX
    async function handleSendMessage() {
        const messageText = userInput.value.trim();
        if (!messageText) return;

        // Clear input area immediately
        userInput.value = "";
        appendMessage(messageText, "user");

        // Create a temporary "Thinking..." bubble for UI responsiveness
        const loadingDiv = document.createElement("div");
        loadingDiv.classList.add("message", "ai");
        loadingDiv.id = "stocksense-loading";
        loadingDiv.innerHTML = `<div class="bubble" style="color: #868e96;"><i class="fa-solid fa-spinner fa-spin"></i> StockSense is calculating...</div>`;
        chatStream.appendChild(loadingDiv);
        chatStream.scrollTop = chatStream.scrollHeight;

        try {
            const response = await fetch("process_ai.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ message: messageText })
            });

            const data = await response.json();
            
            // Remove the thinking placeholder
            const loader = document.getElementById("stocksense-loading");
            if (loader) loader.remove();

            if (data.reply) {
                appendMessage(data.reply, "ai");
            } else if (data.error) {
                appendMessage("Error: " + data.error, "ai");
            }
        } catch (error) {
            const loader = document.getElementById("stocksense-loading");
            if (loader) loader.remove();
            appendMessage("Could not establish Neural Link. Check your network or API settings.", "ai");
            console.error("AI Fetch Error:", error);
        }
    }

    // Trigger on button click
    sendBtn.addEventListener("click", handleSendMessage);

    // Trigger on pressing Enter key (but allow Shift+Enter for line breaks)
    userInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            handleSendMessage();
        }
    });

    // 3. Make the Suggested Question chips clickable
    chips.forEach(chip => {
        chip.addEventListener("click", () => {
            // Strip quotes from chip text for a cleaner query
            const text = chip.innerText.replace(/"/g, "");
            userInput.value = text;
            handleSendMessage();
        });
    });
});