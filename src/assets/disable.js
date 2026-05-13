(function($) {
    'use strict';

    // Init
    $(document).ready(() => {
        login1();
        modal1();
    });

    // Remove login methods from /admin/login page
    const login1 = () => {
        if (location.pathname.match(/\/admin\/login/)) {
            removeMethods();
        }
    };

    // Remove login methods from timeout modal
    const modal1 = () => {
        if (!location.pathname.match(/\/admin\/login/)) {
            const observer1 = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.classList && ['modal', 'alert', 'fitted'].every(cls => node.classList.contains(cls))) {
                            removeMethods();
                            observer1.disconnect();
                        }
                    });
                });
            });

            observer1.observe(document.body, { childList: true, subtree: true });
        }
    };

    const removeMethods = () => {
        const methods = ['#login-form', '.inputcontainer'];
        methods.forEach((m) => {
            $(m).remove();
        });
    };

}(jQuery));
