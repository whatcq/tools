// ==UserScript==
// @name         SSE via Background Script
// @namespace    http://tampermonkey.net/
// @version      1.0
// @description  Establish SSE (EventSource) in background to bypass CSP
// @match        *://*/*
// @grant        GM_registerMenuCommand
// @grant        GM_setValue
// @grant        GM_getValue
// @grant        GM_addValueChangeListener
// @grant        GM_info
// @grant        GM_notification
// @grant        GM_openInTab
// @grant        GM_xmlhttpRequest
// @grant        GM_setClipboard
// @grant        GM_log
// @grant        unsafeWindow
// @grant        window.close
// @grant        window.focus
// @run-at       document-start
// @background
// ==/UserScript==

/*
 * Background script section (runs in a separate context)
 * Here we establish the EventSource connection
 */

(function () {
    if (typeof GM_info === 'undefined' || !GM_info.scriptHandler || GM_info.scriptHandler !== 'Tampermonkey') {
        return;
    }

    // Check if we are in background page
    if (GM_info.isBackground) {
        console.log("[SSE-BG] Background script started");

        const SSE_URL = 'https://example.com/sse'; // Replace with your actual SSE URL

        const es = new EventSource(SSE_URL);

        es.onopen = () => {
            console.log("[SSE-BG] Connected to SSE server.");
        };

        es.onmessage = (event) => {
            console.log("[SSE-BG] Message received:", event.data);

            // Broadcast to all tabs
            GM_setValue('sse_message', {
                id: Date.now(),
                payload: event.data
            });
        };

        es.onerror = (err) => {
            console.error("[SSE-BG] Error occurred:", err);
        };
    }
})();

/*
 * Foreground page (user script on matched pages)
 * This listens to value changes
 */
(function () {
    'use strict';

    GM_addValueChangeListener('sse_message', (key, oldValue, newValue, remote) => {
        if (!remote) return; // Only handle messages from background

        console.log("[SSE-FG] Message from background:", newValue.payload);

        // You can now use this message in your page logic
        // Example: display notification
        GM_notification({
            title: "New SSE Message",
            text: newValue.payload,
            timeout: 5000
        });

        // Optionally trigger your own function
        handleSSEMessage(newValue.payload);
    });

    function handleSSEMessage(data) {
        // Your custom logic here
        console.log("[SSE-FG] Handling SSE data:", data);
        // You can update DOM, send alerts, etc.
    }
})();
