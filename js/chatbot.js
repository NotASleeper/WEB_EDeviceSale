const messageInput = document.querySelector(".message-input");
const chatBody = document.querySelector(".chat-body");
const sendMessageButton = document.querySelector("#send-message");
const chatbotToggler = document.querySelector("#chatbot-toggler");
const closeChatbot = document.querySelector("#close-chatbot");

const API_KEY = "AIzaSyDKzvipA5Fc5Uk3H7YLEDvcuGFHpdz-tFU";
const API_URL = `https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=${API_KEY}`;

const userData = {
    message: null
}

const createMessageElement = (content, ...classes) => {
    const div = document.createElement("div");
    div.classList.add("messaged", ...classes);
    div.innerHTML = content;
    return div;
}

//check if message is a product search
const isProductSearch = (message) => {
    const searchKeywords = ['tìm', 'search', 'sản phẩm', 'product', 'mua', 'buy', 'giá'];
    return searchKeywords.some(keyword => message.toLowerCase().includes(keyword));
}

//search products
const searchProducts = async (keyword) => {
    try {
        const formData = new FormData();
        formData.append('keyword', keyword);

        const response = await fetch('search_product.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
        return { status: 'error', message: 'Đã có lỗi xảy ra khi tìm kiếm sản phẩm.' };
    }
}

//Gemini response
const getGeminiResponse = async (message) => {
    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                contents: [{
                    parts: [{ text: message }]
                }]
            })
        });

        const data = await response.json();
        if (!response.ok) throw new Error(data.error.message);

        return {
            status: 'success',
            message: data.candidates[0].content.parts[0].text.replace(/\*\*(.*?)\*\*/g, "$1").trim()
        };
    } catch (error) {
        console.error('Error:', error);
        return {
            status: 'error',
            message: 'Xin lỗi, tôi đang gặp vấn đề kết nối. Vui lòng thử lại sau.'
        };
    }
}

//truncate if text too long
const truncateText = (text, maxLength) => {
    if (!text) return '';
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

const createProductCard = (product) => {
    let formattedPrice = 'Liên hệ';
    if (product.exp_gadget != null && product.exp_gadget !== undefined) {
        try {
            formattedPrice = Number(product.exp_gadget).toLocaleString() + 'đ';
        } catch (error) {
            console.error('Error formatting price:', error);
            formattedPrice = 'Liên hệ';
        }
    }

    const truncatedName = truncateText(product.name_gadget, 30);

    return `
        <div class="product-card">
            <a href="view_gadget_cus.php?id=${product.id_gadget}" target="_blank">
                <img src="images/img_gadget/${product.pic_gadget || 'path/to/default-image.jpg'}" alt="${product.name_gadget}" style="max-width: 150px; cursor: pointer;">
                <div class="product-info">
                    <div class="product-name">${truncatedName || 'Sản phẩm không có tên'}</div>
                    <div class="product-price">${formattedPrice}</div>
                </div>
            </a>
        </div>
    `;
}

const generateBotResponse = async (incomingMessageDiv) => {
    const messageElement = incomingMessageDiv.querySelector(".message-text");

    try {
        // Check if message is product search
        if (isProductSearch(userData.message)) {
            // Extract search terms (remove search keywords)
            const searchKeywords = ['tìm', 'search', 'sản phẩm', 'product', 'mua', 'buy', 'giá'];
            let searchTerm = userData.message.toLowerCase();
            searchKeywords.forEach(keyword => {
                searchTerm = searchTerm.replace(keyword, '').trim();
            });

            const searchResult = await searchProducts(searchTerm);

            if (searchResult.status === 'success') {
                let responseHtml = '<div>Đây là những sản phẩm phù hợp với yêu cầu của bạn:</div>';
                responseHtml += '<div class="products-container">';
                searchResult.products.forEach(product => {
                    responseHtml += createProductCard(product);
                });
                responseHtml += '</div>';
                messageElement.innerHTML = responseHtml;
            } else if (searchResult.status === 'not_found') {
                // If no products found, fall back to Gemini
                const geminiResponse = await getGeminiResponse(userData.message);
                messageElement.innerText = `Tôi không tìm thấy sản phẩm nào phù hợp. ${geminiResponse.message}`;
            } else {
                messageElement.innerText = searchResult.message;
            }
        } else {
            // Normal conversation with Gemini
            const geminiResponse = await getGeminiResponse(userData.message);
            messageElement.innerText = geminiResponse.message;
        }

    } catch (error) {
        console.error(error);
        messageElement.innerText = 'Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại sau.';
        messageElement.style.color = "#ff0000";
    } finally {
        incomingMessageDiv.classList.remove("thinking");
        chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });
    }
}

const handleOutGoingMessage = (e) => {
    e.preventDefault();
    userData.message = messageInput.value.trim();
    messageInput.value = "";

    const messageContent = `<div class="message-text"></div>`;
    const outgoingMessageDiv = createMessageElement(messageContent, "user-message");
    outgoingMessageDiv.querySelector(".message-text").innerText = userData.message;
    chatBody.appendChild(outgoingMessageDiv);
    chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });

    setTimeout(() => {
        const messageContent = `<svg class="bot-avatar" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
                    <path d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"></path>
                </svg>
                <div class="message-text">
                    <div class="thinking-indicator">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>`;

        const incomingMessageDiv = createMessageElement(messageContent, "bot-message", "thinking");
        chatBody.appendChild(incomingMessageDiv);
        chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });

        generateBotResponse(incomingMessageDiv);
    }, 600);
}

messageInput.addEventListener("keydown", (e) => {
    const userMessage = e.target.value.trim();
    if (e.key === "Enter" && userMessage) {
        handleOutGoingMessage(e);
    }
});

sendMessageButton.addEventListener("click", (e) => handleOutGoingMessage(e));
chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
closeChatbot.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));