// In resources/js/chat-widget.js
export function chatWidget() {
    return {
        // Enhanced polling with exponential backoff
        async pollMessages() {
            if (!this.sessionId || !this.isConnected) return;

            try {
                const response = await fetch(`/api/chat/${this.sessionId}/messages?after=${this.lastMessageId}`);
                const data = await response.json();
                
                if (data.success && data.messages.length > 0) {
                    this.addMessages(data.messages);
                    this.resetPollingInterval(); // Reset to normal interval
                    
                    // Update unread count
                    const unreadMessages = data.messages.filter(msg => 
                        msg.sender_type === 'operator' && !msg.is_read
                    );
                    this.unreadCount += unreadMessages.length;
                    
                    // Play notification sound
                    if (this.enableSound && unreadMessages.length > 0) {
                        this.playNotificationSound();
                    }
                }
            } catch (error) {
                this.increasePollingInterval(); // Exponential backoff
                console.error('Polling failed:', error);
            }
        },

        // Enhanced message sending with retry logic
        async sendMessage() {
            if (!this.currentMessage.trim() || this.isSending) return;

            const messageText = this.currentMessage.trim();
            this.currentMessage = '';
            this.isSending = true;

            // Optimistic UI update
            const tempMessage = {
                id: 'temp-' + Date.now(),
                message: messageText,
                sender_type: 'client',
                created_at: new Date().toISOString(),
                status: 'sending'
            };
            this.messages.push(tempMessage);
            this.scrollToBottom();

            try {
                const response = await fetch(`/api/chat/${this.sessionId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: messageText })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Replace temp message with real one
                    const tempIndex = this.messages.findIndex(m => m.id === tempMessage.id);
                    if (tempIndex !== -1) {
                        this.messages[tempIndex] = data.message;
                    }
                    
                    // Send typing indicator stopped
                    this.sendTypingIndicator(false);
                } else {
                    // Mark as failed
                    tempMessage.status = 'failed';
                    this.showError('Message failed to send');
                }
            } catch (error) {
                tempMessage.status = 'failed';
                this.showError('Connection error');
            } finally {
                this.isSending = false;
            }
        }
    }
}