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
            addMethod('.login-container', 'dashboard')
                .then(() => {
                    console.info('+ google oauth');
                });

            // Display error message if any
            const error = (new URL(location.href)).searchParams.get('error');
            if (error)
                $('.login-errors').append(`${error}. Please contact admin`).removeClass('hidden');

        }
    };

    // Add Google Auth to timeout modal
    const modal = () => {
        if (!location.pathname.match(/\/admin\/login/)) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        // C5
                        if (node.classList && ['modal', 'login-modal', 'fitted'].every(cls => node.classList.contains(cls))) {
                            addMethod(node, location.pathname.replace(/^\/admin\//g, ''))
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

    const addMethod = async (parent, uri) => {
        const obj = await getUrl(uri);
        const link = `   <a href="${obj.url}" target="_self" class="btn google-oauth">\n` +
            `      Sign in with` +
            `   </a>`;

        // C5
        $(parent).find('.alternative-login-methods').append(link);
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
