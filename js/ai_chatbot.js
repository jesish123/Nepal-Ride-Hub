/**
 * Nepal Ride Hub - AI Intelligent Assistant
 * Standalone chatbot with Gemini AI + strong local fallback.
 */

(function () {
    const KNOWLEDGE_BASE = {
        platform: {
            patterns: [
                'what is nepal ride hub',
                'about nepal ride hub',
                'tell me about nepal ride hub',
                'tell me about this website',
                'about this website',
                'website',
                'platform',
                'service',
                'services',
                'what do you offer',
                'company'
            ],
            response: "Nepal Ride Hub is a vehicle rental platform based in Dillibazar, Kathmandu. We provide bikes, cars, SUVs, and Mahindra Thar for self-drive or driver-based rental across Nepal. Our platform supports online booking, customer dashboard, document verification, reviews, and emergency support. Website: www.nepalridehub.com. Contact: 9706421709."
        },

        contact: {
            patterns: [
                'contact',
                'contact number',
                'phone',
                'number',
                'mobile',
                'call',
                'support',
                'customer care',
                'help line',
                'helpline'
            ],
            response: "You can contact Nepal Ride Hub at 9706421709. Our main office is located at Dillibazar, Kathmandu. Website: www.nepalridehub.com. For quick support, share your name, booking date, vehicle type, and issue."
        },

        location: {
            patterns: [
                'location',
                'where is your location',
                'where are you located',
                'where is office',
                'office',
                'address',
                'main location',
                'dillibazar',
                'kathmandu location'
            ],
            response: "Nepal Ride Hub main location is Dillibazar, Kathmandu. We also provide branch service in Kathmandu, Pokhara, Janakpur, Lahan, Butwal, Dang, Palpa, and Jhapa. Website: www.nepalridehub.com. Contact: 9706421709."
        },

        branches: {
            patterns: [
                'branch',
                'branches',
                'where is this branch',
                'available city',
                'cities',
                'area',
                'places available',
                'janakpur',
                'janapur',
                'pokhara',
                'lahan',
                'butwal',
                'dang',
                'palpa',
                'jhapa',
                'from butwal',
                'from janakpur'
            ],
            response: "Nepal Ride Hub branches are available in Kathmandu, Dillibazar, Pokhara, Janakpur, Lahan, Butwal, Dang, Palpa, and Jhapa. You can book from these service areas depending on vehicle availability. Website: www.nepalridehub.com. Main office: Dillibazar, Kathmandu. Contact: 9706421709."
        },

        website: {
            patterns: [
                'website',
                'site',
                'direct link',
                'link',
                'website link',
                'booking link',
                'give me link',
                'send link',
                'official website',
                'official site',
                'nepal ride hub link',
                'where can i book online',
                'online booking link',
                'url'
            ],
            response: "You can visit Nepal Ride Hub directly here: www.nepalridehub.com\n\nTo book online, open the website, go to the Rent a Car page, choose your vehicle, select pickup and return dates, then submit your booking request. For help, contact 9706421709."
        },

        booking: {
            patterns: [
                'how can i book',
                'how to book',
                'book vehicle',
                'book a vehicle',
                'booking process',
                'how do i book',
                'make booking',
                'book me',
                'book me a ride',
                'book me a car',
                'book car',
                'book bike',
                'book thar',
                'book suv',
                'reserve',
                'rent',
                'booking',
                'book',
                'i want to book',
                'i need to book',
                'vehicle booking',
                'car booking',
                'bike booking',
                'how can i book vehicle',
                'how can i book vehivle',
                'how can i book vahicle',
                'how can i book vahicles',
                'how can i book vehicles',
                'step to book',
                'steps to book',
                'booking steps',
                'book from website',
                'tomorrow',
                'tmr',
                '3 days',
                'three days',
                'can i book',
                'can i book this',
                'janakpur booking',
                'book in janakpur',
                'book in janapur',
                'book from butwal'
            ],
            response: "You can book a vehicle from Nepal Ride Hub by following these steps:\n\n1. Open our website: www.nepalridehub.com\n2. Go to the Rent a Car page.\n3. Choose your vehicle such as bike, car, SUV, or Mahindra Thar.\n4. Select your pickup date and return date.\n5. Fill in your required booking details.\n6. Submit the booking request.\n7. Admin will review and confirm your booking.\n8. After confirmation, you can manage your booking from your customer dashboard.\n\nFor quick booking support, contact Nepal Ride Hub at 9706421709."
        },

        pricing: {
            patterns: [
                'price',
                'cost',
                'lowest price',
                'minimum price',
                'cheap',
                'rate',
                'how much',
                'rent price',
                'charges',
                'fee',
                'daily price',
                'per day'
            ],
            response: "Rental price depends on vehicle type, rental duration, pickup location, route, season, and driver option. Bikes are usually lower cost, cars are suitable for comfort, and SUVs or Mahindra Thar are better for hills and off-road routes. For exact price, check the Rent a Car page at www.nepalridehub.com or contact 9706421709."
        },

        vehicles: {
            patterns: [
                'available vehicles',
                'vehicle options',
                'car options',
                'car optinos',
                'what are your car options',
                'cars',
                'suv',
                'fleet',
                'how many vehicle',
                'how many car',
                'how many bike',
                'available car',
                'available bike'
            ],
            response: "Nepal Ride Hub offers bikes, cars, SUVs, and Mahindra Thar for rental. Cars are good for city and family travel, SUVs are better for long routes, and Thar is best for adventure routes like Mustang and Manang. For live availability, check www.nepalridehub.com or contact 9706421709."
        },

        bike: {
            patterns: [
                'bike',
                'bikes',
                'motorbike',
                'motorcycle',
                'what kind of bikes',
                'bike options',
                'types of bikes',
                'which bike'
            ],
            response: "Nepal Ride Hub provides rental bikes for city rides, budget travel, solo travel, and adventure trips. Bikes are suitable for Pokhara, Nagarkot, Mustang, and Manang depending on road condition and rider experience. For exact bike models and availability, check www.nepalridehub.com or contact 9706421709."
        },

        thar: {
            patterns: [
                'thar',
                'mahindra thar',
                'tell me about mahindra thar',
                'book thar',
                'rent thar',
                'thar for mustang',
                'thar for manang'
            ],
            response: "Mahindra Thar is a strong 4x4 vehicle suitable for adventure, hills, rough roads, Mustang, Manang, and group trips. You can book it through www.nepalridehub.com by opening the Rent a Car page, selecting dates, and submitting a request. For help, contact Nepal Ride Hub at 9706421709."
        },

        documents: {
            patterns: [
                'document',
                'documents',
                'license',
                'driving license',
                'permit',
                'id',
                'citizenship',
                'passport',
                'need to bring',
                'verification',
                'kyc',
                'what documents are required'
            ],
            response: "To rent a vehicle, you usually need a valid driving license, citizenship or passport, and an ID copy/photo. For self-drive rental, your license should match the vehicle category."
        },

        payment: {
            patterns: [
                'payment',
                'pay',
                'advance',
                'deposit',
                'cash',
                'online payment',
                'booking payment',
                'security deposit'
            ],
            response: "Payment and deposit depend on the vehicle, rental duration, and booking type. Some self-drive bookings may require advance payment or security deposit. Please confirm payment details from www.nepalridehub.com or contact 9706421709."
        },

        driver: {
            patterns: [
                'driver',
                'with driver',
                'chauffeur',
                'self drive',
                'self-drive',
                'without driver',
                'professional driver'
            ],
            response: "Nepal Ride Hub offers both self-drive and driver options depending on vehicle availability. For difficult routes like Mustang, Manang, hills, or long-distance travel, taking an experienced driver is safer."
        },

        travel: {
            patterns: [
                'visit',
                'places',
                'tour',
                'destination',
                'recommend',
                'travel',
                'trip',
                'best places',
                'pokhara',
                'mustang',
                'chitwan',
                'nagarkot',
                'manang',
                'which vehicle is better',
                'best vehicle',
                'for hills',
                'for family',
                'for city',
                'for offroad',
                'off road'
            ],
            response: "For city travel, a car is comfortable. For family or group trips, SUV is better. For hills, rough roads, Mustang, Manang, and adventure routes, Mahindra Thar is a strong option. For budget or solo travel, bike can be suitable depending on road condition."
        },

        emergency: {
            patterns: [
                'emergency',
                'accident',
                'breakdown',
                'help',
                'stuck',
                'police',
                'problem on road',
                'emergency help'
            ],
            response: "For emergency support, first move to a safe place. Then contact Nepal Ride Hub at 9706421709 and share your booking details, vehicle number, location, and problem. For serious danger, also contact local police or ambulance."
        },

        cancel: {
            patterns: [
                'cancel',
                'refund',
                'change date',
                'reschedule',
                'modify booking'
            ],
            response: "Booking cancellation or date change depends on timing and vehicle availability. You can manage your booking from the dashboard or contact Nepal Ride Hub at 9706421709 for support."
        },

        account: {
            patterns: [
                'account',
                'login',
                'signup',
                'sign up',
                'register',
                'dashboard',
                'profile'
            ],
            response: "Customers can create an account or log in to manage bookings, upload documents, check booking status, update profile details, and view rental information from the dashboard."
        },

        reviews: {
            patterns: [
                'review',
                'rating',
                'feedback',
                'customer review',
                'experience'
            ],
            response: "Nepal Ride Hub has a review system where customers can share ratings and feedback after using the service. Reviews help other users understand vehicle quality, service experience, and booking reliability."
        },

        greetings: {
            patterns: [
                'hello',
                'hi',
                'hey',
                'greetings',
                'namaste',
                'good morning',
                'good afternoon',
                'good evening',
                'how are you'
            ],
            response: "Namaste! I am doing well and ready to help you with Nepal Ride Hub vehicle rental, booking, branches, prices, documents, website link, or travel support."
        },

        thanks: {
            patterns: [
                'thank',
                'thanks',
                'ok',
                'okay',
                'nice',
                'good',
                'great'
            ],
            response: "You're welcome! I can also help you with booking, vehicle selection, prices, documents, branches, website link, or travel recommendations."
        },

        default: "I can help you with Nepal Ride Hub services, vehicle booking, prices, documents, branches, contact details, website link, travel routes, driver options, and emergency support. Please ask your question clearly."
    };

    const createChatbotUI = () => {
        const style = document.createElement('style');
        style.textContent = `
            #ai-chatbot-container {
                position: fixed;
                bottom: 30px;
                right: 30px;
                z-index: 10000;
                font-family: 'Inter', Arial, sans-serif;
            }

            #chatbot-window {
                width: 380px;
                height: 500px;
                background: #ffffff;
                border-radius: 20px;
                box-shadow: 0 15px 50px rgba(0,0,0,0.15);
                display: none;
                flex-direction: column;
                overflow: hidden;
                border: 1px solid rgba(0,0,0,0.05);
                transform: translateY(20px);
                transition: all 0.3s ease;
            }

            #chatbot-window.active {
                display: flex;
                transform: translateY(0);
            }

            #chatbot-header {
                background: linear-gradient(135deg, #3561ff, #1a45e4);
                color: #ffffff;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            #chatbot-header img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                border: 2px solid rgba(255,255,255,0.2);
            }

            #chatbot-messages {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 15px;
                background: #f8f9fa;
                scroll-behavior: smooth;
            }

            .chat-msg {
                max-width: 82%;
                padding: 12px 16px;
                border-radius: 15px;
                font-size: 0.9rem;
                line-height: 1.45;
                white-space: pre-line;
                word-wrap: break-word;
            }

            .msg-bot {
                background: #ffffff;
                color: #333333;
                align-self: flex-start;
                border-bottom-left-radius: 2px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.04);
            }

            .msg-user {
                background: #3561ff;
                color: #ffffff;
                align-self: flex-end;
                border-bottom-right-radius: 2px;
            }

            #chatbot-input-area {
                padding: 15px;
                background: #ffffff;
                border-top: 1px solid #eeeeee;
                display: flex;
                gap: 10px;
            }

            #chatbot-input {
                flex: 1;
                border: 1px solid #dddddd;
                border-radius: 50px;
                padding: 10px 18px;
                outline: none;
                font-size: 0.9rem;
            }

            #chatbot-input:focus {
                border-color: #3561ff;
            }

            #chatbot-send {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #3561ff;
                color: #ffffff;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.2s;
            }

            #chatbot-send:hover {
                transform: scale(1.08);
            }

            #chatbot-toggle {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: #3561ff;
                box-shadow: 0 8px 25px rgba(53, 97, 255, 0.4);
                border: none;
                cursor: pointer;
                color: #ffffff;
                font-size: 1.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s;
                margin-left: auto;
            }

            #chatbot-toggle:hover {
                transform: scale(1.05);
            }

            .typing-dot {
                width: 6px;
                height: 6px;
                background: #888888;
                border-radius: 50%;
                display: inline-block;
                animation: typing 1.4s infinite;
                margin-right: 3px;
            }

            .typing-dot:nth-child(2) {
                animation-delay: 0.2s;
            }

            .typing-dot:nth-child(3) {
                animation-delay: 0.4s;
            }

            @keyframes typing {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }

            #chatbot-suggestions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 10px;
            }

            .suggestion-chip {
                background: #ffffff;
                border: 1px solid #3561ff;
                color: #3561ff;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.2s;
                white-space: nowrap;
            }

            .suggestion-chip:hover {
                background: #3561ff;
                color: #ffffff;
            }

            @media (max-width: 480px) {
                #ai-chatbot-container {
                    bottom: 20px;
                    right: 15px;
                }

                #chatbot-window {
                    width: 92vw;
                    height: 500px;
                }
            }
        `;
        document.head.appendChild(style);

        const container = document.createElement('div');
        container.id = 'ai-chatbot-container';

        container.innerHTML = `
            <div id="chatbot-window">
                <div id="chatbot-header">
                    <img src="https://ui-avatars.com/api/?name=Assistant&background=fff&color=3561ff" alt="AI">
                    <div>
                        <div style="font-weight: 700; font-size: 1rem;">Nepal Ride Hub AI</div>
                        <div style="font-size: 0.75rem; opacity: 0.85;">Online | Ready to Help</div>
                    </div>
                </div>

                <div id="chatbot-messages"></div>

                <div id="chatbot-input-area">
                    <input type="text" id="chatbot-input" placeholder="Ask about vehicle rentals...">
                    <button id="chatbot-send" title="Send">➤</button>
                </div>
            </div>

            <button id="chatbot-toggle" title="Chat">💬</button>
        `;

        document.body.appendChild(container);

        const windowEl = document.getElementById('chatbot-window');
        const toggleBtn = document.getElementById('chatbot-toggle');
        const inputEl = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send');
        const messagesEl = document.getElementById('chatbot-messages');

        const addMessage = (text, isUser = false) => {
            const msg = document.createElement('div');
            msg.className = `chat-msg ${isUser ? 'msg-user' : 'msg-bot'}`;
            msg.textContent = text;
            messagesEl.appendChild(msg);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        };

        const showSuggestions = () => {
            const suggestions = [
                "What is Nepal Ride Hub?",
                "Where is your location?",
                "Give me direct link",
                "How can I book vehicle?",
                "What are your branches?",
                "Contact number?",
                "What documents are required?",
                "Which vehicle is best for hills?",
                "Tell me about Mahindra Thar",
                "Emergency help!"
            ];

            const suggestionBox = document.createElement('div');
            suggestionBox.id = 'chatbot-suggestions';

            suggestions.forEach(text => {
                const chip = document.createElement('div');
                chip.className = 'suggestion-chip';
                chip.textContent = text;

                chip.onclick = () => {
                    inputEl.value = text;
                    handleSend();
                    suggestionBox.remove();
                };

                suggestionBox.appendChild(chip);
            });

            messagesEl.appendChild(suggestionBox);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        };

        const showTyping = () => {
            const typing = document.createElement('div');
            typing.id = 'chatbot-typing';
            typing.className = 'chat-msg msg-bot';
            typing.innerHTML = '<span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>';
            messagesEl.appendChild(typing);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            return typing;
        };

        const normalizeText = (text) => {
            return text
                .toLowerCase()
                .replace(/[?.,!]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        };

        const keywordMatch = (text, pattern) => {
            const escaped = pattern.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

            if (pattern.length <= 3) {
                return new RegExp(`\\b${escaped}\\b`, 'i').test(text);
            }

            return text.includes(pattern);
        };

        const getFallbackResponse = (input) => {
            const lowerInput = normalizeText(input);

            if (
                lowerInput.includes('who am i') ||
                lowerInput.includes('my account') ||
                lowerInput.includes('my profile') ||
                lowerInput.includes('my details') ||
                lowerInput.includes('who is logged in')
            ) {
                const u = window.CHATBOT_USER || { logged_in: false };
                if (!u.logged_in) {
                    return "You are not currently logged in, so I can't access your account details.\nPlease log in first to view your profile and bookings.\n👉 Login: /login.php\nNew here? Register: /register.php";
                } else if (u.role === 'admin') {
                    return `You are ${u.name}, the System Administrator of Nepal Ride Hub.\nYou have full access to manage users, bookings, vehicles, reviews, and all system settings.`;
                } else {
                    return `You are ${u.name}, a registered customer on Nepal Ride Hub.\nYour account email is ${u.email} and your phone number is ${u.phone || 'not provided'}.\nYou can manage your profile and view your bookings from your customer dashboard.`;
                }
            }

            if (
                lowerInput.includes('direct link') ||
                lowerInput.includes('website link') ||
                lowerInput.includes('booking link') ||
                lowerInput.includes('give me link') ||
                lowerInput.includes('send link') ||
                lowerInput.includes('official website') ||
                lowerInput.includes('official site') ||
                lowerInput.includes('online booking link') ||
                lowerInput.includes('nepal ride hub link') ||
                lowerInput.includes('url')
            ) {
                return KNOWLEDGE_BASE.website.response;
            }

            if (
                lowerInput.includes('book') ||
                lowerInput.includes('booking') ||
                lowerInput.includes('reserve') ||
                lowerInput.includes('rent') ||
                lowerInput.includes('tmr') ||
                lowerInput.includes('tomorrow') ||
                lowerInput.includes('3 days') ||
                lowerInput.includes('three days') ||
                lowerInput.includes('vehivle') ||
                lowerInput.includes('vahicle') ||
                lowerInput.includes('vahicles')
            ) {
                return KNOWLEDGE_BASE.booking.response;
            }

            if (
                lowerInput.includes('price') ||
                lowerInput.includes('cost') ||
                lowerInput.includes('lowest') ||
                lowerInput.includes('minimum') ||
                lowerInput.includes('cheap') ||
                lowerInput.includes('rate') ||
                lowerInput.includes('how much')
            ) {
                return KNOWLEDGE_BASE.pricing.response;
            }

            if (
                lowerInput.includes('thar') ||
                lowerInput.includes('mahindra')
            ) {
                return KNOWLEDGE_BASE.thar.response;
            }

            if (
                lowerInput.includes('what kind of bike') ||
                lowerInput.includes('bike options') ||
                lowerInput.includes('types of bike') ||
                lowerInput === 'bikes' ||
                lowerInput === 'bike'
            ) {
                return KNOWLEDGE_BASE.bike.response;
            }

            if (
                lowerInput.includes('branch') ||
                lowerInput.includes('janakpur') ||
                lowerInput.includes('janapur') ||
                lowerInput.includes('butwal') ||
                lowerInput.includes('pokhara') ||
                lowerInput.includes('lahan') ||
                lowerInput.includes('dang') ||
                lowerInput.includes('palpa') ||
                lowerInput.includes('jhapa')
            ) {
                return KNOWLEDGE_BASE.branches.response;
            }

            if (
                lowerInput.includes('best vehicle') ||
                lowerInput.includes('which vehicle') ||
                lowerInput.includes('for hills') ||
                lowerInput.includes('for family') ||
                lowerInput.includes('for city') ||
                lowerInput.includes('off road') ||
                lowerInput.includes('offroad') ||
                lowerInput.includes('travel') ||
                lowerInput.includes('trip')
            ) {
                return KNOWLEDGE_BASE.travel.response;
            }

            for (const key in KNOWLEDGE_BASE) {
                if (
                    key !== 'default' &&
                    KNOWLEDGE_BASE[key].patterns &&
                    KNOWLEDGE_BASE[key].patterns.some(pattern => keywordMatch(lowerInput, pattern))
                ) {
                    return KNOWLEDGE_BASE[key].response;
                }
            }

            return KNOWLEDGE_BASE.default;
        };

        let discoveredModel = null;

        const getAIResponse = async (input) => {
            /*
             * IMPORTANT:
             * Do not expose your real API key publicly in frontend code for production.
             * For college/local testing, you can paste your key below.
             * For real website, use backend API protection.
             */
            const API_KEY = 'AIzaSyDhtUTpdk-XAHBov5SMHYnBimgQTkRyBxM';

            if (!API_KEY || API_KEY === '' || API_KEY === 'PASTE_YOUR_GEMINI_API_KEY_HERE') {
                return getFallbackResponse(input);
            }

            try {
                if (!discoveredModel) {
                    for (const ver of ['v1', 'v1beta']) {
                        try {
                            const listResponse = await fetch(
                                `https://generativelanguage.googleapis.com/${ver}/models?key=${API_KEY.trim()}`
                            );

                            if (listResponse.ok) {
                                const listData = await listResponse.json();

                                const found = listData.models?.find(model =>
                                    model.supportedGenerationMethods &&
                                    model.supportedGenerationMethods.includes('generateContent')
                                );

                                if (found) {
                                    discoveredModel = {
                                        ver: ver,
                                        path: found.name
                                    };
                                    break;
                                }
                            }
                        } catch (e) { }
                    }
                }

                const apiVer = discoveredModel?.ver || 'v1beta';
                const modelPath = discoveredModel?.path || 'models/gemini-1.5-flash';

                const u = window.CHATBOT_USER || { logged_in: false };
                let userStateStr = '';

                if (!u.logged_in) {
                    userStateStr = `SESSION STATE: No user is currently logged in.

RULE — "Who am I?" or identity questions:
If the user asks "who am i", "my account", "my profile",
"my bookings", "my details", or anything about their personal info,
you MUST respond with:
"You are not currently logged in, so I can't access your account details.
Please log in first to view your profile and bookings.
👉 Login: /login.php
New here? Register: /register.php"

Never guess or make up any user information.`;
                } else if (u.role === 'admin') {
                    userStateStr = `SESSION STATE: An Administrator is logged in.
Admin Details:
  - Name    : ${u.name}
  - Email   : ${u.email}
  - Admin ID: ${u.id}
  - Role    : System Administrator

RULE — "Who am I?" or identity questions:
If the admin asks "who am i", "my account", "my role", "my details",
respond like:
"You are ${u.name}, the System Administrator of Nepal Ride Hub.
You have full access to manage users, bookings, vehicles, reviews,
and all system settings."

Always treat this person with admin-level respect.`;
                } else if (u.role === 'user') {
                    userStateStr = `SESSION STATE: A registered customer is logged in.
Customer Details:
  - Name   : ${u.name}
  - Email  : ${u.email}
  - Phone  : ${u.phone}
  - User ID: ${u.id}
  - Role   : Customer

RULE — "Who am I?" or identity questions:
If the user asks "who am i", "my account", "my profile",
"my details", "who is logged in", respond like:
"You are ${u.name}, a registered customer on Nepal Ride Hub.
Your account email is ${u.email} and your phone number is ${u.phone || 'not provided'}.
You can manage your profile and view your bookings from your customer dashboard."`;
                }

                const aiPrompt = `
You are the official AI assistant of Nepal Ride Hub.

${userStateStr}

Your job:
Answer the user's question correctly and directly about Nepal Ride Hub.

Very important rules:
- Understand the user's exact question first.
- Answer according to what the user asked.
- Keep answers short, clear, and helpful.
- Use 2 to 6 lines maximum.
- Do not write long essays.
- Do not repeat unnecessary details.
- If user asks in simple English, broken English, Roman Nepali, or mixed Nepali-English, still understand and answer clearly.
- If user asks unrelated questions like math, coding, politics, or personal topics, politely say you mainly help with Nepal Ride Hub vehicle rental services.
- Do not invent exact vehicle numbers, exact prices, or exact vehicle models if not provided.
- For exact price, model, and live availability, tell user to check Rent a Car page or contact Nepal Ride Hub.
- If user asks "book me", explain the booking process. Do not say booking is completed.
- If user asks how to book a vehicle, give clear step-by-step booking process.
- If user asks for direct link, website link, booking link, URL, or official website, give www.nepalridehub.com.
- If user asks about a city/branch, mention whether Nepal Ride Hub has that branch.
- If user asks about "vehicle", "car", "bike", "Thar", or "SUV", answer about that vehicle category.
- If user asks about emergency, give emergency support guidance and contact number.
- You can give simple helpful ideas about Nepal Ride Hub, such as which vehicle is better for city, family, adventure, hills, or long route.
- Do not go too deep or give unrelated long explanations.
- Keep the answer focused on Nepal Ride Hub services.

Nepal Ride Hub official details:
Name: Nepal Ride Hub
Official Website: www.nepalridehub.com
Contact Number: 9706421709
Main Location: Dillibazar, Kathmandu
Branches: Kathmandu, Dillibazar, Pokhara, Janakpur, Lahan, Butwal, Dang, Palpa, Jhapa

Platform services:
- Vehicle rental across Nepal
- Bikes, Cars, SUVs, and Mahindra Thar
- Self-drive and driver options
- Online booking system
- Customer dashboard
- Document verification
- Reviews and rating system
- Emergency support
- Travel and destination recommendations

Booking process:
1. Open www.nepalridehub.com.
2. Go to the Rent a Car page.
3. Choose a vehicle such as bike, car, SUV, or Mahindra Thar.
4. Select pickup date and return date.
5. Fill in the required booking details.
6. Submit the booking request.
7. Admin reviews and confirms the booking.
8. Customer can manage booking from the dashboard.

Required documents:
- Valid driving license
- Citizenship or passport
- Valid ID copy/photo

Vehicle guidance:
- Bike: good for solo travel, city rides, budget trips, and adventure routes depending on road condition.
- Car: good for city travel, family travel, Pokhara, Nagarkot, and comfortable road trips.
- SUV: good for family, group, long-distance travel, and rougher roads.
- Mahindra Thar: best for Mustang, Manang, hills, off-road routes, and adventure trips.
- For Chitwan, car or SUV is suitable.
- For group travel, SUV or larger vehicle is better.

Example behavior:
User: "how can i book vehicle give me process"
Answer: "You can book a vehicle from Nepal Ride Hub by following these steps:
1. Open www.nepalridehub.com
2. Go to the Rent a Car page.
3. Choose your vehicle.
4. Select pickup and return dates.
5. Submit the booking request.
6. Admin will review and confirm it.
For quick help, call 9706421709."

User: "give me direct link"
Answer: "You can visit Nepal Ride Hub directly here: www.nepalridehub.com"

User: "what kind of bikes do you offer"
Answer: "Nepal Ride Hub offers rental bikes for city rides, budget travel, solo trips, and adventure routes. Exact bike models may change by availability, so please check the Rent a Car page or contact 9706421709."

User: "from butwal?"
Answer: "Yes, Nepal Ride Hub has branch service in Butwal. You can request vehicle booking from Butwal depending on availability. Contact: 9706421709."

User: "which vehicle is better for hills"
Answer: "For hills and rough roads, SUV or Mahindra Thar is better. Mahindra Thar is especially suitable for off-road and adventure routes like Mustang and Manang."

Current user question:
${input}
                `;

                const response = await fetch(
                    `https://generativelanguage.googleapis.com/${apiVer}/${modelPath}:generateContent?key=${API_KEY.trim()}`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            contents: [
                                {
                                    parts: [
                                        {
                                            text: aiPrompt
                                        }
                                    ]
                                }
                            ]
                        })
                    }
                );

                if (!response.ok) {
                    const err = await response.json().catch(() => ({}));
                    console.warn(
                        `AI API Warning: ${response.status} - ${err.error?.message || 'API issue'}`
                    );
                    return getFallbackResponse(input);
                }

                const data = await response.json();
                const answer = data.candidates?.[0]?.content?.parts?.[0]?.text;

                if (!answer) {
                    return getFallbackResponse(input);
                }

                return answer.trim();

            } catch (error) {
                console.error("AI Connection Error:", error.message);
                return getFallbackResponse(input);
            }
        };

        const handleSend = async () => {
            const text = inputEl.value.trim();

            if (!text) return;

            addMessage(text, true);
            inputEl.value = '';

            const typingEl = showTyping();

            try {
                const aiResponse = await getAIResponse(text);
                addMessage(aiResponse);
            } catch (err) {
                addMessage(getFallbackResponse(text));
            } finally {
                if (typingEl && typingEl.parentNode) {
                    typingEl.remove();
                }
            }
        };

        toggleBtn.onclick = () => {
            windowEl.classList.toggle('active');

            if (windowEl.classList.contains('active')) {
                if (messagesEl.children.length === 0) {
                    addMessage("Namaste! I'm your Nepal Ride Hub Assistant. I can help with vehicle rentals, booking steps, website link, prices, documents, branches, routes, and emergency support. How can I help you today?");
                    setTimeout(showSuggestions, 500);
                }

                inputEl.focus();
            }
        };

        sendBtn.onclick = handleSend;

        inputEl.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                handleSend();
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createChatbotUI);
    } else {
        createChatbotUI();
    }
})();