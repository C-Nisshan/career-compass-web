// Newsletter form
document.querySelector('.newsletter form').addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Subscribed! Thank you for joining!');
});

// Chatbot toggle
const chatbot = document.querySelector('.chatbot');
chatbot.addEventListener('click', () => {
    alert('Chat with us! (Feature coming soon!)');
});
