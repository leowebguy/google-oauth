(function($) {
    'use strict';

    // Init
    $(document).ready(() => {
        login();
        modal();
    });

    // Add Google Auth to /admin/login page
    const login = () => {
        if (location.pathname.match(/\/admin\/login/)) {
            addMethod('.login-container')
                .then(() => {
                    console.info('+ google sso');
                });

            // Display error message if any
            const error = (new URL(location.href)).searchParams.get('error');
            if (error)
                $('#login-errors').append(`${error}<br>Please contact admin`);

        }
    };

    // Add Google Auth to timeout modal
    const modal = () => {
        if (!location.pathname.match(/\/admin\/login/)) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.classList && ['modal', 'login-modal'].every(cls => node.classList.contains(cls))) {
                            addMethod('.login-modal-form')
                                .then(() => {
                                    observer.disconnect();
                                });
                        }
                    });
                });
            });

            observer.observe(document.body, { childList: true, subtree: true });
        }
    };

    const addMethod = async (parent = 'document') => {
        const obj = await getUrl();
        const link = `   <a href="${obj.url}" target="_self" class="btn google-oauth">\n` +
            `      Sign in with` +
            `   </a>`;

        // C5
        //$(parent).find('.alternative-login-methods').append(link);

        // C4
        $(link).insertAfter('#login-errors');
    };

    // Gen Auth URL
    const getUrl = async () => {
        const response = await fetch(`/oauth/g/url`);
        if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

        const obj = response.json();
        if (!obj || typeof obj !== 'object')
            throw new Error('Invalid response');

        return obj || {};
    };

}(jQuery));
