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
            addMethod('.login-container', '#login-errors', 'dashboard')
                .then(() => {
                    console.info('+ google oauth');
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
                        // C4
                        if (node.classList && ['modal', 'alert', 'fitted'].every(cls => node.classList.contains(cls))) {
                            addMethod(node, 'p.error', location.pathname.replace(/^\/admin\//g, ''))
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

    const addMethod = async (parent = 'document', after, uri) => {
        const obj = await getUrl(uri);
        const link = `   <a href="${obj.url}" target="_self" class="btn google-oauth">\n` +
            `      Sign in with` +
            `   </a>`;

        // C4
        $(link).insertAfter(after);
    };

    // Gen Auth URL
    const getUrl = async (uri) => {
        const response = await fetch(`/oauth/g/url?uri=${uri}`);
        if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

        const obj = response.json();
        if (!obj || typeof obj !== 'object')
            throw new Error('Invalid response');

        return obj || {};
    };

}(jQuery));
